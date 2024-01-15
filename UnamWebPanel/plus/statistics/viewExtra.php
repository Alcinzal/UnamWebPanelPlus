<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

$ExtraQuery = getConn()->query("SELECT st_date, st_totalMiners, st_totalOnline, st_totalOffline, st_totalActive, st_totalIdle, st_totalStarting, st_totalPaused, st_totalStopped, st_totalError, st_totalVRAM, st_totalUnknown FROM statistics");
$miners = $ExtraQuery->fetchAll(PDO::FETCH_ASSOC);

$existingDataExtra = array();

foreach ($miners as $miner) {
    $date = $miner['st_date'];

    $existingDataExtra[$date] = array(
        'Total Miners' => $miner['st_totalMiners'],
        'Total Offline' => $miner['st_totalOffline'],
        'Total Idle' => $miner['st_totalIdle'],
        'Total Online' => $miner['st_totalOnline'],
        'Total Error' => $miner['st_totalError'],
        'Total Unknown' => $miner['st_totalUnknown'],
        'Total VRAM' => $miner['st_totalVRAM'],
        'Total Stopped' => $miner['st_totalStopped'],
        'Total Starting' => $miner['st_totalStarting'],
        'Total Active' => $miner['st_totalActive'],
        'Total Paused' => $miner['st_totalPaused']
    );
}

?>
<h2>Total Extra</h2>
<div class="statisticsOptions">
    <label for="timeIntervalExtra">Interval:</label>
    <select id="timeIntervalExtra">
        <option value="hourly">Hourly</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
    </select>
    <div class="tooltipPlus">?
        <span style="width: 250px;" class="tooltiptextPlus">Gets average values based on chosen interval. Might result
            in impractical values.</span>
    </div>
</div>
<div style="background-color:#343a40;">
    <canvas id="chartExtra"></canvas>
</div>

<script src='../UnamWebPanel/assets/modules/chartjs/chart.umd.js'></script>
<script src='../UnamWebPanel/plus/plusFunctions.js'></script>

<script>
    var myChartExtra = null;

    // Your PHP array
    var existingDataExtra = <?php echo json_encode($existingDataExtra); ?>;

    // Extract labels and datasets from processed data
    var mainLabelsExtra = Object.keys(existingDataExtra);

    if (mainLabelsExtra.sort()[0]) {
        // Get the first date in the file and current date
        firstDateExtra = new Date(mainLabelsExtra.sort()[0].split(' ').slice(0, 2).join(' '))
        currentDateExtra = new Date()

        // Fill in empty dates up to today
        do {
            if (!mainLabelsExtra.includes(formatDate(firstDateExtra))) {
                mainLabelsExtra.push(formatDate(firstDateExtra))
            }
            firstDateExtra.addHours(1)
        } while (!(formatDate(firstDateExtra) > formatDate(currentDateExtra)))

        mainLabelsExtra.sort();
    }

    localStorageCreator()

    document.getElementById('timeIntervalExtra').addEventListener('change', updateChartExtra);
    var defaultIntervalExtra = JSON.parse(localStorage.getItem('TotalExtra'))['defaultInterval'];

    if (defaultIntervalExtra) {
        document.getElementById('timeIntervalExtra').value = defaultIntervalExtra;
    }

    updateChartExtra()

    function localStorageCreator() {
        var name = "TotalExtra"
        var TotalExtra = localStorage.getItem(name);

        if (TotalExtra) {
            try {
                TotalExtra = JSON.parse(TotalExtra)
            }
            catch {
                TotalExtra = {}
            }
        }
        else {
            TotalExtra = {}
        }

        if (!(TotalExtra['defaultInterval'] !== undefined)) {
            TotalExtra['defaultInterval'] = document.getElementById('timeIntervalExtra').value;
        }

        TotalExtra['Legends'] = TotalExtra['Legends'] || {};

        for (let date in existingDataExtra) {
            for (let label in existingDataExtra[date]) {
                if (!(TotalExtra['Legends'][label] !== undefined)) {
                    TotalExtra['Legends'][label] = null
                }
            }
        }

        localStorage.setItem(name, JSON.stringify(TotalExtra));
    }

    function updateChartExtra() {
        var selectedInterval = document.getElementById('timeIntervalExtra').value;
        var TotalExtra = JSON.parse(localStorage.getItem('TotalExtra'));
        TotalExtra['defaultInterval'] = selectedInterval
        localStorage.setItem('TotalExtra', JSON.stringify(TotalExtra));

        if (selectedInterval == "hourly") {
            dataHourlyExtra();
        }
        else {
            dataAverageExtra()
        }
    }

    function dataHourlyExtra() {
        var hourlyKeys = new Set();

        mainLabelsExtra.forEach(label => {
            if (existingDataExtra[label]) {
                Object.keys(existingDataExtra[label]).forEach(key => {
                    hourlyKeys.add(key);
                });
            }
            else {
                existingDataExtra[label] = 0;
            }
        });

        createChartExtra(hourlyKeys, mainLabelsExtra, existingDataExtra);
    }

    function dataAverageExtra() {
        var selectedInterval = document.getElementById('timeIntervalExtra').value;

        var Values = {};
        var Keys = new Set();
        var ValuesCount = {};

        mainLabelsExtra.forEach(label => {
            if (selectedInterval == "daily") {
                date = label.split(' ').slice(0, 1).join(' ')
            }
            else if (selectedInterval == "weekly") {
                date = label.split('-').slice(0, 1).join(' ') + '-W' + label.split(' ').slice(2, 3).join(' ')
            }
            else if (selectedInterval == "monthly") {
                date = label.split('-').slice(0, 2).join('-')
            }

            if (!Values[date]) {
                Values[date] = {};
            }

            if (!ValuesCount[date]) {
                ValuesCount[date] = 0;
            }

            if (existingDataExtra[label]) {
                ValuesCount[date]++;
                Object.keys(existingDataExtra[label]).forEach(key => {
                    Values[date][key] = (Values[date][key] + existingDataExtra[label][key] || existingDataExtra[label][key] || 0);
                    Keys.add(key);
                });
            }
        });



        Labels = Object.keys(Values)
        Labels.forEach(date => {
            Keys.forEach(key => {
                Values[date][key] = Math.round(Values[date][key] / ValuesCount[date]);
            });
        });

        createChartExtra(Keys, Labels, Values);
    }

    function createChartExtra(keys, labels, values) {
        const datasets = Array.from(keys).map(key => {
            var totalExtraData = JSON.parse(localStorage.getItem("TotalExtra"));

            return {
                label: key,
                data: labels.map(label => values[label][key] || 0),
                fill: true,
                hidden: totalExtraData ? totalExtraData['Legends'][key] : null,
            };
        });

        if (myChartExtra != null) {
            myChartExtra.clear();
            myChartExtra.destroy();
        }

        const ctx = document.getElementById('chartExtra');

        myChartExtra = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: "white",
                        },
                        onClick: function (e, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            if (ci.isDatasetVisible(index)) {
                                ci.hide(index);
                                legendItem.hidden = true;
                            } else {
                                ci.show(index);
                                legendItem.hidden = false;
                            }

                            var totalExtra = JSON.parse(localStorage.getItem("TotalExtra"));

                            totalExtra['Legends'][legendItem.text] = legendItem.hidden

                            localStorage.setItem("TotalExtra", JSON.stringify(totalExtra));
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: "white",
                            autoSkip: true,
                            maxRotation: 0
                        }
                    },
                    y: {
                        min: 0,
                        ticks: {
                            color: "white",
                        }
                    }
                },
            },
        });
    }
</script>
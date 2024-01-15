<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

$hashrateQuery = getConn()->query("SELECT st_date, st_hashrate FROM statistics");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$existingDataHashrate = array();

foreach ($miners as $miner) {
    $date = $miner['st_date'];
    $hashrateData = json_decode($miner['st_hashrate'], true);

    foreach ($hashrateData as $key => $value) {
        $existingDataHashrate[$date][$key] = $value;
    }
}

?>
<h2>Total Hashrate</h2>
<div class="statisticsOptions">
    <label for="timeIntervalHashrate">Interval:</label>
    <select id="timeIntervalHashrate">
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
    <canvas id="chartHashrate"></canvas>
</div>

<script src='../UnamWebPanel/assets/modules/chartjs/chart.umd.js'></script>
<script src='../UnamWebPanel/plus/plusFunctions.js'></script>

<script>
    var myChartHashrate = null;

    // Your PHP array
    var existingDataHashrate = <?php echo json_encode($existingDataHashrate); ?>;

    // Extract labels and datasets from processed data
    var mainLabelsHashrate = Object.keys(existingDataHashrate);

    if (mainLabelsHashrate.sort()[0]) {
        // Get the first date in the file and current date
        firstDateHashrate = new Date(mainLabelsHashrate.sort()[0].split(' ').slice(0, 2).join(' '))
        currentDateHashrate = new Date()

        // Fill in empty dates up to today
        do {
            if (!mainLabelsHashrate.includes(formatDate(firstDateHashrate))) {
                mainLabelsHashrate.push(formatDate(firstDateHashrate))
            }
            firstDateHashrate.addHours(1)
        } while (!(formatDate(firstDateHashrate) > formatDate(currentDateHashrate)))

        mainLabelsHashrate.sort();
    }

    localStorageCreator()

    document.getElementById('timeIntervalHashrate').addEventListener('change', updateChartHashrate);
    var defaultIntervalHashrate = JSON.parse(localStorage.getItem('TotalHashrate'))['defaultInterval'];

    if (defaultIntervalHashrate) {
        document.getElementById('timeIntervalHashrate').value = defaultIntervalHashrate;
    }

    updateChartHashrate()

    function localStorageCreator() {
        var name = "TotalHashrate"
        var TotalHashrate = localStorage.getItem(name);

        if (TotalHashrate) {
            try {
                TotalHashrate = JSON.parse(TotalHashrate)
            }
            catch {
                TotalHashrate = {}
            }
        }
        else {
            TotalHashrate = {}
        }

        if (!(TotalHashrate['defaultInterval'] !== undefined)) {
            TotalHashrate['defaultInterval'] = document.getElementById('timeIntervalHashrate').value;
        }

        TotalHashrate['Legends'] = TotalHashrate['Legends'] || {};

        for (let date in existingDataHashrate) {
            for (let label in existingDataHashrate[date]) {
                if (!(TotalHashrate['Legends'][label] !== undefined)) {
                    TotalHashrate['Legends'][label] = null
                }
            }
        }

        localStorage.setItem(name, JSON.stringify(TotalHashrate));
    }

    function updateChartHashrate() {
        var selectedInterval = document.getElementById('timeIntervalHashrate').value;
        var TotalHashrate = JSON.parse(localStorage.getItem('TotalHashrate'));
        TotalHashrate['defaultInterval'] = selectedInterval
        localStorage.setItem('TotalHashrate', JSON.stringify(TotalHashrate));

        if (selectedInterval == "hourly") {
            dataHourlyHashrate();
        }
        else {
            dataAverageHashrate()
        }
    }

    function dataHourlyHashrate() {
        var hourlyKeys = new Set();

        mainLabelsHashrate.forEach(label => {
            if (existingDataHashrate[label]) {
                Object.keys(existingDataHashrate[label]).forEach(key => {
                    hourlyKeys.add(key);
                });
            }
            else {
                existingDataHashrate[label] = 0;
            }
        });

        createChartHashrate(hourlyKeys, mainLabelsHashrate, existingDataHashrate);
    }

    function dataAverageHashrate() {
        var selectedInterval = document.getElementById('timeIntervalHashrate').value;

        var Values = {};
        var Keys = new Set();
        var ValuesCount = {};

        mainLabelsHashrate.forEach(label => {
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

            if (existingDataHashrate[label]) {
                ValuesCount[date]++;
                Object.keys(existingDataHashrate[label]).forEach(key => {
                    Values[date][key] = (Values[date][key] + existingDataHashrate[label][key] || existingDataHashrate[label][key] || 0);
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

        createChartHashrate(Keys, Labels, Values);
    }

    function createChartHashrate(keys, labels, values) {
        const datasets = Array.from(keys).map(key => {
            var totalHashrateData = JSON.parse(localStorage.getItem("TotalHashrate"));

            return {
                label: key,
                data: labels.map(label => values[label][key] || 0),
                fill: true,
                hidden: totalHashrateData ? totalHashrateData['Legends'][key] : null,
            };
        });

        if (myChartHashrate != null) {
            myChartHashrate.clear();
            myChartHashrate.destroy();
        }

        const ctx = document.getElementById('chartHashrate');

        myChartHashrate = new Chart(ctx, {
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

                            var totalHashrate = JSON.parse(localStorage.getItem("TotalHashrate"));

                            totalHashrate['Legends'][legendItem.text] = legendItem.hidden

                            localStorage.setItem("TotalHashrate", JSON.stringify(totalHashrate));
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
                            callback: function (value, index, values) {
                                // Format Hashrate
                                if (value >= 1000000000000) {
                                    return (value / 1000000000000).toFixed(1) + ' TH/s';
                                } else if (value >= 1000000000) {
                                    return (value / 1000000000).toFixed(1) + ' GH/s';
                                } else if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + ' MH/s';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + ' KH/s';
                                } else {
                                    return value.toFixed(1) + ' H/s';
                                }
                            }
                        }
                    }
                },
            },
        });
    }
</script>
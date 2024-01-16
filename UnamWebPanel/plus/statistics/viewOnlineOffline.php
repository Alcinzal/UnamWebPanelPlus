<?php
require_once dirname(__DIR__, 2) . '/security.php';

$OnlineOfflineQuery = getConn()->query("SELECT st_date, st_onlineMiners, st_offlineMiners FROM statistics");
$miners = $OnlineOfflineQuery->fetchAll(PDO::FETCH_ASSOC);

$existingDataOnline = array();
$existingDataOffline = array();

foreach ($miners as $miner) {
    $date = $miner['st_date'];
    $OnlineData = json_decode($miner['st_onlineMiners'], true);
    $OfflineData = json_decode($miner['st_offlineMiners'], true);


    foreach ($OnlineData as $key => $value) {
        $existingDataOnline[$date][$key] = $value;
    }

    foreach ($OfflineData as $key => $value) {
        $existingDataOffline[$date][$key] = $value;
    }
}

?>
<h2>Total Online/Offline Miners</h2>
<div class="statisticsOptions">
    <label for="chooseOnlineOffline">Miners:</label>
    <select id="chooseOnlineOffline">
        <option value="online">Online</option>
        <option value="offline">Offline</option>
    </select>
    <br>
    <label for="timeIntervalOnlineOffline">Interval:</label>
    <select id="timeIntervalOnlineOffline">
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
    <canvas id="chartOnlineOffline"></canvas>
</div>

<script>
    var myChartOnlineOffline = null;

    document.getElementById('chooseOnlineOffline').addEventListener('change', function () {
        refreshOnlineOffline(document.getElementById('chooseOnlineOffline').value);
    }, false);

    refreshOnlineOffline(document.getElementById('chooseOnlineOffline').value);

    function refreshOnlineOffline(chooseOnlineOffline) {
        var existingDataOnlineOffline = null;

        if (chooseOnlineOffline === 'online')
        {
            existingDataOnlineOffline = <?php echo json_encode($existingDataOnline); ?>;
        }
        else if (chooseOnlineOffline === 'offline')
        {
            existingDataOnlineOffline = <?php echo json_encode($existingDataOffline); ?>;
        }

        // Extract labels and datasets from processed data
        var mainLabelsOnlineOffline = Object.keys(existingDataOnlineOffline);

        if (mainLabelsOnlineOffline.sort()[0]) {
            // Get the first date in the file and current date
            firstDateOnlineOffline = new Date(mainLabelsOnlineOffline.sort()[0].split(' ').slice(0, 2).join(' '))
            currentDateOnlineOffline = new Date()

            // Fill in empty dates up to today
            do {
                if (!mainLabelsOnlineOffline.includes(formatDate(firstDateOnlineOffline))) {
                    mainLabelsOnlineOffline.push(formatDate(firstDateOnlineOffline))
                }
                firstDateOnlineOffline.addHours(1)
            } while (!(formatDate(firstDateOnlineOffline) > formatDate(currentDateOnlineOffline)))

            mainLabelsOnlineOffline.sort();
        }

        localStorageCreator()

        document.getElementById('timeIntervalOnlineOffline').addEventListener('change', updateChartOnlineOffline);
        var defaultIntervalOnlineOffline = JSON.parse(localStorage.getItem('TotalOnlineOffline'))['defaultInterval'];

        if (defaultIntervalOnlineOffline) {
            document.getElementById('timeIntervalOnlineOffline').value = defaultIntervalOnlineOffline;
        }

        updateChartOnlineOffline()

        function localStorageCreator() {
            var name = "TotalOnlineOffline"
            var TotalOnlineOffline = localStorage.getItem(name);

            if (TotalOnlineOffline) {
                try {
                    TotalOnlineOffline = JSON.parse(TotalOnlineOffline)
                }
                catch {
                    TotalOnlineOffline = {}
                }
            }
            else {
                TotalOnlineOffline = {}
            }

            if (!(TotalOnlineOffline['defaultInterval'] !== undefined)) {
                TotalOnlineOffline['defaultInterval'] = document.getElementById('timeIntervalOnlineOffline').value;
            }

            TotalOnlineOffline['Legends'] = TotalOnlineOffline['Legends'] || {};

            for (let date in existingDataOnlineOffline) {
                for (let label in existingDataOnlineOffline[date]) {
                    if (!(TotalOnlineOffline['Legends'][label] !== undefined)) {
                        TotalOnlineOffline['Legends'][label] = null
                    }
                }
            }

            localStorage.setItem(name, JSON.stringify(TotalOnlineOffline));
        }

        function updateChartOnlineOffline() {
            var selectedInterval = document.getElementById('timeIntervalOnlineOffline').value;
            var TotalOnlineOffline = JSON.parse(localStorage.getItem('TotalOnlineOffline'));
            TotalOnlineOffline['defaultInterval'] = selectedInterval
            localStorage.setItem('TotalOnlineOffline', JSON.stringify(TotalOnlineOffline));

            if (selectedInterval == "hourly") {
                dataHourlyOnlineOffline();
            }
            else {
                dataAverageOnlineOffline()
            }
        }

        function dataHourlyOnlineOffline() {
            var hourlyKeys = new Set();

            mainLabelsOnlineOffline.forEach(label => {
                if (existingDataOnlineOffline[label]) {
                    Object.keys(existingDataOnlineOffline[label]).forEach(key => {
                        hourlyKeys.add(key);
                    });
                }
                else {
                    existingDataOnlineOffline[label] = 0;
                }
            });

            createChartOnlineOffline(hourlyKeys, mainLabelsOnlineOffline, existingDataOnlineOffline);
        }

        function dataAverageOnlineOffline() {
            var selectedInterval = document.getElementById('timeIntervalOnlineOffline').value;

            var Values = {};
            var Keys = new Set();
            var ValuesCount = {};

            mainLabelsOnlineOffline.forEach(label => {
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

                if (existingDataOnlineOffline[label]) {
                    ValuesCount[date]++;
                    Object.keys(existingDataOnlineOffline[label]).forEach(key => {
                        Values[date][key] = (Values[date][key] + existingDataOnlineOffline[label][key] || existingDataOnlineOffline[label][key] || 0);
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

            createChartOnlineOffline(Keys, Labels, Values);
        }

        function createChartOnlineOffline(keys, labels, values) {
            const datasets = Array.from(keys).map(key => {
                var totalOnlineOfflineData = JSON.parse(localStorage.getItem("TotalOnlineOffline"));

                return {
                    label: key,
                    data: labels.map(label => values[label][key] || 0),
                    fill: true,
                    hidden: totalOnlineOfflineData ? totalOnlineOfflineData['Legends'][key] : null,
                };
            });

            if (myChartOnlineOffline != null) {
                myChartOnlineOffline.clear();
                myChartOnlineOffline.destroy();
            }

            const ctx = document.getElementById('chartOnlineOffline');

            myChartOnlineOffline = new Chart(ctx, {
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

                                var totalOnlineOffline = JSON.parse(localStorage.getItem("TotalOnlineOffline"));

                                totalOnlineOffline['Legends'][legendItem.text] = legendItem.hidden

                                localStorage.setItem("TotalOnlineOffline", JSON.stringify(totalOnlineOffline));
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
                                color: "white"
                            }
                        }
                    },
                },
            });
        }
    }
</script>
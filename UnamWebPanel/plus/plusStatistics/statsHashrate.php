<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';

$statsLocationHashrate = dirname(__DIR__, 2) . "/plus/plusStatistics/hashrateHour.json";

//$statsLocationHashrate = dirname(__DIR__, 2) . "/plus/plusStatistics/output.json";

$existingDataHashrate = file_exists($statsLocationHashrate) ? json_decode(file_get_contents($statsLocationHashrate), true) : array();

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
        <span style="width: 250px;" class="tooltiptextPlus">Gets average values based on chosen interval</span>
    </div>
</div>
<div style="background-color:#343a40;">
    <canvas id="chartHashrate"></canvas>
</div>

<script src='../UnamWebPanel/assets/modules/chartjs/chart.umd.js'></script>
<script src='../UnamWebPanel/plus/plusFunctions.js'></script>

<script>
    document.getElementById('timeIntervalHashrate').addEventListener('change', updateChartHashrate);
    var defaultIntervalHashrate = localStorage.getItem('defaultIntervalHashrate');

    if (defaultIntervalHashrate) {
        document.getElementById('timeIntervalHashrate').value = defaultIntervalHashrate;
    }

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

    updateChartHashrate()

    function updateChartHashrate() {
        var selectedValueHashrate = document.getElementById('timeIntervalHashrate').value;
        localStorage.setItem('defaultIntervalHashrate', selectedValueHashrate);

        var selectedInterval = document.getElementById('timeIntervalHashrate').value;

        if (selectedInterval == "hourly") {
            dataHourlyHashrate();
        }
        else {
            dataAverageHashrate()
        }
    }

    function dataHourlyHashrate() {
        var hourlyKeys = new Set();

        // Collect all keys from all data points
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

        // Create datasets based on all available keys
        const datasets = Array.from(hourlyKeys).map(key => ({
            label: key,
            data: mainLabelsHashrate.map(label => existingDataHashrate[label][key] || 0), // Use 0 if the key is not present
            fill: true,
        }));

        createChartHashrate(datasets, mainLabelsHashrate);
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
        //Get the average
        Labels.forEach(date => {
            Keys.forEach(key => {
                Values[date][key] = Values[date][key] / ValuesCount[date];
            });
        });

        const datasets = Array.from(Keys).map(key => ({
            label: key,
            data: Labels.map(label => Values[label][key] || 0), // Use 0 if the key is not present
            fill: true,
        }));

        createChartHashrate(datasets, Labels);
    }

    function createChartHashrate(datasets, labels) {
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
                        }
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
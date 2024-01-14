<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

$statsLocationExtra = dirname(__DIR__, 2) . "/plus/plusStatistics/extraHour.json";

//$statsLocationExtra = dirname(__DIR__, 2) . "/plus/plusStatistics/output.json";

$existingDataExtra = file_exists($statsLocationExtra) ? json_decode(file_get_contents($statsLocationExtra), true) : array();

?>
<h2>Total Extra (WIP)</h2>
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
    document.getElementById('timeIntervalExtra').addEventListener('change', updateChartExtra);
    var defaultIntervalExtra = localStorage.getItem('defaultIntervalExtra');

    if (defaultIntervalExtra) {
        document.getElementById('timeIntervalExtra').value = defaultIntervalExtra;
    }

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

    updateChartExtra()

    function updateChartExtra() {
        var selectedValueExtra = document.getElementById('timeIntervalExtra').value;
        localStorage.setItem('defaultIntervalExtra', selectedValueExtra);

        var selectedInterval = document.getElementById('timeIntervalExtra').value;

        if (selectedInterval == "hourly") {
            dataHourlyExtra();
        }
        else {
            dataAverageExtra()
        }
    }

    function dataHourlyExtra() {
        var hourlyKeys = new Set();

        // Collect all keys from all data points
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

        // Create datasets based on all available keys
        const datasets = Array.from(hourlyKeys).map(key => ({
            label: key,
            data: mainLabelsExtra.map(label => existingDataExtra[label][key] || 0), // Use 0 if the key is not present
            fill: true,
        }));

        createChartExtra(datasets, mainLabelsExtra);
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
        //Get the average
        Labels.forEach(date => {
            Keys.forEach(key => {
                Values[date][key] = Math.round(Values[date][key] / ValuesCount[date]);
            });
        });

        const datasets = Array.from(Keys).map(key => ({
            label: key,
            data: Labels.map(label => Values[label][key] || 0), // Use 0 if the key is not present
            fill: true,
        }));

        createChartExtra(datasets, Labels);
    }

    function createChartExtra(datasets, labels) {
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
                        }
                    }
                },
            },
        });
    }
</script>
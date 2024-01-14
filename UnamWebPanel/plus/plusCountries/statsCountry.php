<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

$CountriesQuery = getConn()->query("SELECT ms_algorithm, ms_Hashrate, ms_lastConnection, ms_status, ms_creationDate, ms_ip FROM miners");
$miners = $CountriesQuery->fetchAll(PDO::FETCH_ASSOC);

$totalMiners = count($miners);

$statsLocationCountries = dirname(__DIR__, 2) . "/plus/plusCountries/statsCountry.json";

$existingDataCountries = file_exists($statsLocationCountries) ? json_decode(file_get_contents($statsLocationCountries), true) : array();

?>
<div class="plus-section">
    <div class="plus-box">
        <h2>Options</h2>
        <div class="countriesOptions">
            <p>To refresh the countries, click the button below.</p>
            <p>Might take a while, depending on how many miners you have.</p>
            <?php
            echo '<b>Total Miners: ' . $totalMiners . '</b><br>';
            echo '<b>Estimated refresh time: ' . round($totalMiners * 0.43, 0) . ' seconds</b><br><br>';
            ?>
            <button id="refreshCountries" class="plus-btn">Refresh countries</button>
            <br>
            <br>
            <p id="statusCountries">Status: Not started</p>
        </div>
    </div>
    <div style="height: 400px; width: 400px;" class="plus-box">
        <h2>Total Countries</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartCountries"></canvas>
        </div>
    </div>
</div>

<script>
    document.getElementById('refreshCountries').addEventListener('click', refreshCountries);

    var totalMiners = <?php echo $totalMiners ?>;

    var statusCountries = document.getElementById('statusCountries');

    var started = false;

    var intervalId;

    var myChartCountry = null;

    function refreshCountries() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                statusCountries.textContent = xhr.responseText;
                started = false;
                clearInterval(intervalId);
                createChartCountry()
            }
        }
        xhr.open("POST", "plus/plusCountries/statsCountrySaver.php");
        xhr.send();

        started = true;

        intervalId = setInterval(checkCountries, 500);
    }

    function checkCountries() {
        if (started) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    statusCountries.textContent = xhr.responseText + "/" + totalMiners;
                }
            }
            xhr.open("POST", "plus/plusCountries/statsCountryCheck.php");
            xhr.send();
        }
    }

    function createChartCountry() {
        if (myChartCountry != null) {
            myChartCountry.clear();
            myChartCountry.destroy();
        }

        var timestamp = new Date().getTime();

        fetch('plus/plusCountries/statsCountry.json?t=' + timestamp)
            .then((response) => response.json())
            .then((json) => {
                // Get the JSON data from PHP
                var statsData = json;

                // Extract labels and data from the JSON
                var labels = Object.keys(statsData);
                var data = Object.values(statsData);

                // Create a pie chart
                var ctx = document.getElementById('chartCountries').getContext('2d');
                myChartCountry = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        title: {
                            display: true,
                            text: 'Country Stats'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                        }
                    }
                });
            });
    }

    createChartCountry();
</script>
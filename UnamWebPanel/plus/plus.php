<?php
require_once dirname(__DIR__, 1).'/assets/php/templates.php';
require_once dirname(__DIR__, 1).'/security.php';

// TOTAL HASHRATE
echo '<h1>Total Hashrate</h1>';
include("plus/totalHashrate/totalHashrate.php");


//STATISTICS
echo '<h1>Statistics<h1>';
include("plus/statistics/statsViewer.php");

//GEO
echo '<h1>Geo<h1>';
include("plus/geo/geo.php");
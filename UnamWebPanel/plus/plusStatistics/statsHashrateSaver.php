<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';

// Query to get hashrate information from miners table
$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

// Get unique algorithms
$algorithms = array_unique(array_column($miners, 'ms_algorithm'));

// Array to store total hashrate for each algorithm
$totalHashrateData = array();

// Current hour in "Y-m-d H:00:00 W" format
$currentHour = date("Y-m-d H:00:00 W");

foreach ($algorithms as $algorithm) {
    $totalHashrate = 0;

    foreach ($miners as $miner) {
        if ($miner['ms_algorithm'] === $algorithm) {
            // Check if the last connection was less than 3 minutes ago
            $lastConnection = strtotime($miner['ms_lastConnection']);
            $currentTime = time();
            if ($currentTime - $lastConnection < 180 && $miner['ms_status'] == 2) {
                $totalHashrate += $miner['ms_hashrate'];
            }
        }
    }
    $totalHashrateData[$algorithm] = $totalHashrate;
}

// Read existing data from the file or create an empty array
$existingData = file_exists(dirname(__DIR__, 2) . "/plus/plusStatistics/hashrateHour.json") ? json_decode(file_get_contents(dirname(__DIR__, 2) . "/plus/plusStatistics/hashrateHour.json"), true) : array();

// Add or overwrite the total hashrate data for the current hour
$existingData[$currentHour] = $totalHashrateData;

// Sort the array by keys (dates and times)
ksort($existingData);

// Save the data back to the file
file_put_contents(dirname(__DIR__, 2) . "/plus/plusStatistics/hashrateHour.json", json_encode($existingData, JSON_PRETTY_PRINT));


?>
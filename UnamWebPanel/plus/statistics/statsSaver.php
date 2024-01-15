<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/class/db.php';
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';

$st_date = date("Y-m-d H:00:00 W");
$st_hashrate = array();
$st_totalMiners = 0;
$st_totalOnline = 0;
$st_totalOffline = 0;
$st_onlineMiners = array();
$st_offlineMiners = array();
$st_totalActive = 0;
$st_totalIdle = 0;
$st_totalStarting = 0;
$st_totalPaused = 0;
$st_totalStopped = 0;
$st_totalError = 0;
$st_totalVRAM = 0;
$st_totalUnknown = 0;


$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status, ms_creationDate, ms_ip FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$st_totalMiners = count($miners);


foreach ($miners as $item) {
    $minerLastConnection = new DateTime($item['ms_lastConnection']);
    $currentDate = new DateTime();
    $difference = $currentDate->getTimestamp() - $minerLastConnection->getTimestamp();

    if ($difference < 180) {
        switch ($item['ms_status']) {
            case 1:
                $st_totalStopped++;
                break;
            case 2:
                if (!isset($st_onlineMiners[$item['ms_algorithm']])) {
                    $st_onlineMiners[$item['ms_algorithm']] = 0;
                }
                $st_onlineMiners[$item['ms_algorithm']]++;
                $st_totalActive++;
                $st_totalOnline++;
                break;
            case 3:
                $st_totalIdle++;
                $st_totalOnline++;
                break;
            case 4:
                $st_totalPaused++;
                break;
            case 5:
                $st_totalVRAM++;
                break;
            case 6:
                $st_totalStarting++;
                break;
            case 7:
                $st_totalError++;
                break;
            case -1:
                $st_totalOffline++;
                break;
            default:
                $st_totalUnknown++;
                break;
        }
    } else {
        if (!isset($st_offlineMiners[$item['ms_algorithm']])) {
            $st_offlineMiners[$item['ms_algorithm']] = 0;
        }
        $st_offlineMiners[$item['ms_algorithm']]++;
        $st_totalOffline++;
    }
}


// Get unique algorithms
$algorithms = array_unique(array_column($miners, 'ms_algorithm'));

foreach ($algorithms as $algorithm) {
    $totalHashrate = 0;

    foreach ($miners as $miner) {
        if ($miner['ms_algorithm'] === $algorithm) {
            $minerLastConnection = new DateTime($item['ms_lastConnection']);
            $currentDate = new DateTime();
            $difference = $currentDate->getTimestamp() - $minerLastConnection->getTimestamp();

            if ($difference < 180 && $miner['ms_status'] == 2) {
                $totalHashrate += $miner['ms_hashrate'];
            }
        }
    }
    $st_hashrate[$algorithm] = $totalHashrate;
}



$st_hashrate = json_encode($st_hashrate);
$st_onlineMiners = json_encode($st_onlineMiners);
$st_offlineMiners = json_encode($st_offlineMiners);

// Check if the st_date already exists in the database
$checkIfExists = getConn()->prepare("SELECT COUNT(*) FROM statistics WHERE st_date = ?");
$checkIfExists->execute([$st_date]);
$rowCount = $checkIfExists->fetch(PDO::FETCH_ASSOC);
$checkIfExists->closeCursor();

if ($rowCount > 0) {
    // If the date exists, delete the existing row
    $deleteExisting = getConn()->prepare("DELETE FROM statistics WHERE st_date = ?");
    $deleteExisting->execute([$st_date]);
    $deleteExisting->closeCursor();
}


// Now, perform the INSERT operation
$addStats = getConn()->prepare("INSERT INTO statistics (st_date, st_hashrate, st_totalMiners, st_totalOnline, st_totalOffline, st_onlineMiners, st_offlineMiners, st_totalActive, st_totalIdle, st_totalStarting, st_totalPaused, st_totalStopped, st_totalError, st_totalVRAM, st_totalUnknown) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$addStats->execute([$st_date, $st_hashrate, $st_totalMiners, $st_totalOnline, $st_totalOffline, $st_onlineMiners, $st_offlineMiners, $st_totalActive, $st_totalIdle, $st_totalStarting, $st_totalPaused, $st_totalStopped, $st_totalError, $st_totalVRAM, $st_totalUnknown]);
$addStats->closeCursor();
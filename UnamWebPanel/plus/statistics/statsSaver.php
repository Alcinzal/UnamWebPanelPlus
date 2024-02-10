<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/class/db.php';

$st_date = strtotime(date("Y-m-d H:00:00"));
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


$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$st_totalMiners = count($miners);


foreach ($miners as $miner) {
    $minerLastConnection = $miner['ms_lastConnection'];
    $difference = time() - $minerLastConnection;

    if ($difference < 180) {
        $st_totalOnline++;

        switch ($miner['ms_status']) {
            case 1:
                $st_totalStopped++;
                break;
            case 2:
                $st_totalActive++;
                break;
            case 3:
                $st_totalIdle++;
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
            default:
                $st_totalUnknown++;
                break;
        }

        if ($miner['ms_status'] == 2 || $miner['ms_status'] == 3) {
            if (!isset($st_onlineMiners[$miner['ms_algorithm']])) {
                $st_onlineMiners[$miner['ms_algorithm']] = 0;
            }
            $st_onlineMiners[$miner['ms_algorithm']]++;

            if (!isset($st_hashrate[$miner['ms_algorithm']])) {
                $st_hashrate[$miner['ms_algorithm']] = 0;
            }
            $st_hashrate[$miner['ms_algorithm']] += $miner['ms_hashrate'];
        }

    } else {
        if (!isset($st_offlineMiners[$miner['ms_algorithm']])) {
            $st_offlineMiners[$miner['ms_algorithm']] = 0;
        }
        $st_offlineMiners[$miner['ms_algorithm']]++;
        $st_totalOffline++;
    }
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
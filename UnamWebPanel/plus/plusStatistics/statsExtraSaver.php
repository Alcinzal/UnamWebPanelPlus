<?php
require_once dirname(__DIR__, 2) . '/assets/php/templates.php';

$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status, ms_creationDate, ms_ip FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$extraData = array();


$extraData['Total Miners'] = count($miners);

foreach ($miners as $item) {
    $minerCreationDate = new DateTime($item['ms_creationDate']);
    $minerLastConnection = new DateTime($item['ms_lastConnection']);
    $currentDate = new DateTime();
    $difference = $currentDate->getTimestamp() - $minerLastConnection->getTimestamp();

    if ($difference < 180) {
        switch ($item['ms_status']) {
            case 1:
                $extraData['Total Stopped']++;
                break;
            case 2:
                $extraData['Online Miners'][$item['ms_algorithm']]++;
                $extraData['Total Online']++;
                break;
            case 3:
                $extraData['Total Idle']++;
                break;
            case 4:
                $extraData['Total Paused']++;
                break;
            case 5:
                $extraData['Total VRAM']++;
                break;
            case 6:
                $extraData['Total Starting']++;
                break;
            case 7:
                $extraData['Total Error']++;
                break;
            case -1:
                $extraData['Total Offline']++;
                break;
            default:
                $extraData['Total Unknown']++;
                break;
        }
    } else {
        $extraData['Offline Miners'][$item['ms_algorithm']]++;
        $extraData['Total Offline']++;
    }
}

$currentHour = date("Y-m-d H:00:00 W");

$existingData = file_exists(dirname(__DIR__, 2) . "/plus/plusStatistics/extraHour.json") ? json_decode(file_get_contents(dirname(__DIR__, 2) . "/plus/plusStatistics/extraHour.json"), true) : array();

$existingData[$currentHour] = $extraData;

ksort($existingData);

file_put_contents(dirname(__DIR__, 2) . "/plus/plusStatistics/extraHour.json", json_encode($existingData, JSON_PRETTY_PRINT));
?>
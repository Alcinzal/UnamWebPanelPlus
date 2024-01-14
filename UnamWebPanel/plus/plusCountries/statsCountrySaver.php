<?php
//http://country.io/names.json
//http://country.io/continent.json

require_once dirname(__DIR__, 2) . '/assets/php/templates.php';
require_once dirname(__DIR__, 2) . '/security.php';

//Clear file so the status starts at zero
file_put_contents("statsCountry.json", "{\"Unknown\": 0}");

$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status, ms_creationDate, ms_ip FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$totalCountries = array();

$countries = get_object_vars(json_decode(file_get_contents('http://country.io/names.json')));

foreach ($miners as $item) {
    $itemCountry = file_get_contents('https://api.country.is/'.$item['ms_ip']);
    if ($itemCountry === FALSE){
        $totalCountries['Unknown']++;
    }
    else {
        $itemCountry = get_object_vars(json_decode($itemCountry))['country'];
        $itemCountry = $countries[$itemCountry];
        $totalCountries[$itemCountry]++;
    }

    arsort($totalCountries);

    file_put_contents('statsCountry.json', json_encode($totalCountries, JSON_PRETTY_PRINT));
}

echo 'Status: Done';
?>

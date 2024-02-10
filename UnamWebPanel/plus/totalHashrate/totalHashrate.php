<?php
require_once dirname(__DIR__, 2).'/assets/php/security.php';

$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

$algorithms = array_unique(array_column($miners, 'ms_algorithm'));

function calculateTotalHashrate($algorithm, $miners)
{
    $informationAlgorithms = array();

    $totalHashrate = 0;
    $totalMiners = 0;
    $averageHashrate = 0;

    foreach ($miners as $miner) {
        if ($miner['ms_algorithm'] === $algorithm) {
            $lastConnection = $miner['ms_lastConnection'];
            $currentTime = time();

            if ($currentTime - $lastConnection < 180 && ($miner['ms_status'] == 2 || $miner['ms_status'] == 3)) {
                $totalHashrate += $miner['ms_hashrate'];
                $totalMiners += 1;
            }
        }
    }
    try {
        $averageHashrate = round($totalHashrate/$totalMiners);
    }
    catch(DivisionByZeroError $e) {
        $averageHashrate = 0;
    }

    $informationAlgorithms[0] = $totalHashrate;
    $informationAlgorithms[1] = $totalMiners;
    $informationAlgorithms[2] = $averageHashrate;

    return $informationAlgorithms;
}

echo '<div class="plus-section">';

if($algorithms)
{
    foreach ($algorithms as $algorithm) {
        $informationAlgorithms = calculateTotalHashrate($algorithm, $miners);
    
        $isHashrate = ($informationAlgorithms[0] > 0) ? ' isHashrate' : '';
    
        $isMiners = ($informationAlgorithms[1] > 0) ? ' isMiners' : '';

        echo '<div class="algorithm-box' . $isHashrate . $isMiners . '">
                    <div class="algorithm-header">
                    <h2>' . $algorithm . '</h2>
                    </div>
                    <div class="algorithm-footer">
                    <p>Total Hashrate: ' . unamFormatHashrate($informationAlgorithms[0]) . '</p>
                    <p>Total Mining: ' . $informationAlgorithms[1] . '</p>
                    <p>Average per miner: ' . unamFormatHashrate($informationAlgorithms[2]) . '</p>
                    </div>
                  </div>';
    }
}
else 
{   
    echo '<div class="plus-box">
    <div class="algorithm-header">
    <h2>No algorithms found</h2>
    </div>
    <div class="algorithm-footer">
    <p>Total Hashrate: ' . unamFormatHashrate(0) . '</p>
    <p>Total Mining: ' . 0 . '</p>
    <p>Average per miner: ' . unamFormatHashrate(0) . '</p>
    </div>
  </div>';
}
echo '</div>';

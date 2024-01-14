<?php
function calculateTotalHashrate($algorithm, $miners)
{
    $informationAlgorithms = array();

    $totalHashrate = 0;
    $totalMiners = 0;
    $averageHashrate = 0;

    foreach ($miners as $miner) {
        if ($miner['ms_algorithm'] === $algorithm) {
            // Check if the last connection was less than 3 minutes ago
            $lastConnection = strtotime($miner['ms_lastConnection']);
            $currentTime = time();

            if ($currentTime - $lastConnection < 180 && $miner['ms_status'] == 2) {
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

// Query to get hashrate information from miners table
$hashrateQuery = getConn()->query("SELECT ms_algorithm, ms_hashrate, ms_lastConnection, ms_status FROM miners");
$miners = $hashrateQuery->fetchAll(PDO::FETCH_ASSOC);

// Get unique algorithms
$algorithms = array_unique(array_column($miners, 'ms_algorithm'));


echo '<div class="algorithm-section">';

if($algorithms)
{
    foreach ($algorithms as $algorithm) {
        $informationAlgorithms = calculateTotalHashrate($algorithm, $miners);
    
        $isActive = ($informationAlgorithms[0] > 0) ? ' isActive' : '';
    
        echo '<div class="algorithm-box' . $isActive . '">
                    <div class="algorithm-header">
                    <h2>' . $algorithm . '</h2>
                    </div>
                    <div class="algorithm-footer">
                    <p>Total Hashrate: ' . unamtFormatHashrate($informationAlgorithms[0]) . '</p>
                    <p>Total Miners: ' . $informationAlgorithms[1] . '</p>
                    <p>Average per miner: ' . unamtFormatHashrate($informationAlgorithms[2]) . '</p>
                    </div>
                  </div>';
    }
}
else 
{   
    echo '<div class="algorithm-box">
    <div class="algorithm-header">
    <h2>No algorithms found</h2>
    </div>
    <div class="algorithm-footer">
    <p>Total Hashrate: ' . unamtFormatHashrate(0) . '</p>
    <p>Total Miners: ' . 0 . '</p>
    <p>Average per miner: ' . unamtFormatHashrate(0) . '</p>
    </div>
  </div>';
}
echo '</div>';


?>
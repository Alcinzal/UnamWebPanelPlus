<?php
require_once dirname(__DIR__, 2).'/assets/php/security.php';

$statsQuery = getConn()->query("SELECT st_date, st_hashrate, st_totalMiners, st_totalOnline, st_totalOffline, st_onlineMiners, st_offlineMiners, st_totalActive, st_totalIdle, st_totalStarting, st_totalPaused, st_totalStopped, st_totalError, st_totalVRAM, st_totalUnknown FROM statistics");
$stats = $statsQuery->fetchAll(PDO::FETCH_ASSOC);

$statsDataTime = array();
$statsDataExtra = array();
$statsDataHashrate = array();
$statsDataOnlineOffline = array();

foreach ($stats as $stat) {
    $time = $stat['st_date'];

    $statsDataTime[$time] = array();

    $date = date('Y-m-d H:00:00', $time);

    $statsDataExtra[$date] = array(
        'Total Miners' => $stat['st_totalMiners'],
        'Total Offline' => $stat['st_totalOffline'],
        'Total Mining (Idle)' => $stat['st_totalIdle'],
        'Total Online' => $stat['st_totalOnline'],
        'Total Error' => $stat['st_totalError'],
        'Total Unknown' => $stat['st_totalUnknown'],
        'Total VRAM' => $stat['st_totalVRAM'],
        'Total Stopped' => $stat['st_totalStopped'],
        'Total Starting' => $stat['st_totalStarting'],
        'Total Mining (Active)' => $stat['st_totalActive'],
        'Total Paused' => $stat['st_totalPaused'],
        'Total Mining' => $stat['st_totalActive'] + $stat['st_totalIdle']
    );

    $statsDataHashrate[$date] = json_decode($stat['st_hashrate']);

    $statsDataOnlineOffline[$date] = array(
        'Online Miners' => json_decode($stat['st_onlineMiners'], true),
        'Offline Miners' => json_decode($stat['st_offlineMiners'], true)
    );
}

ksort($statsDataTime);

$statsFirstTime = array_keys($statsDataTime)[0] ?? strtotime(date("Y-m-d H:00:00"));
$currentHour = strtotime(date("Y-m-d H:00:00"));

if ($statsFirstTime) {
    while ($statsFirstTime < $currentHour) {
        $statsFirstTime = $statsFirstTime + 60 * 60;
        $statsFirstDate = date('Y-m-d H:00:00', $statsFirstTime);
        $statsDataExtra[$statsFirstDate] = $statsDataExtra[$statsFirstDate] ?? array();
        $statsDataHashrate[$statsFirstDate] = $statsDataHashrate[$statsFirstDate] ?? array();
        $statsDataOnlineOffline[$statsFirstDate] = $statsDataOnlineOffline[$statsFirstDate] ?? array();
    }
}

ksort($statsDataHashrate);
$statsFullDataHashrate['hourly'] = $statsDataHashrate;
$statsFullDataHashrate['daily'] = getAverage($statsDataHashrate, 'daily');
$statsFullDataHashrate['weekly'] = getAverage($statsDataHashrate, 'weekly');
$statsFullDataHashrate['monthly'] = getAverage($statsDataHashrate, 'monthly');

ksort($statsDataExtra);
$statsFullDataExtra['hourly'] = $statsDataExtra;
$statsFullDataExtra['daily'] = getAverage($statsDataExtra, 'daily');
$statsFullDataExtra['weekly'] = getAverage($statsDataExtra, 'weekly');
$statsFullDataExtra['monthly'] = getAverage($statsDataExtra, 'monthly');

ksort($statsDataOnlineOffline);
$statsFullDataOnlineOffline['hourly'] = $statsDataOnlineOffline;
$statsFullDataOnlineOffline['daily'] = getAverage($statsDataOnlineOffline, 'daily', true);
$statsFullDataOnlineOffline['weekly'] = getAverage($statsDataOnlineOffline, 'weekly', true);
$statsFullDataOnlineOffline['monthly'] = getAverage($statsDataOnlineOffline, 'monthly', true);


function getAverage($data, $interval, $status = false)
{
    $dataAverage = array();

    foreach ($data as $date => $values) {
        $dateFormat = null;

        if ($interval == "daily") {
            $dateFormat = date('Y-m-d', strtotime($date));
        } elseif ($interval == "weekly") {
            $dateFormat = date('Y \WW', strtotime($date));
        } elseif ($interval == "monthly") {
            $dateFormat = date('Y-m', strtotime($date));
        }

        $dataAverage[$dateFormat] = $dataAverage[$dateFormat] ?? array();

        foreach ($values as $key => $value) {
            if ($status == true) {
                foreach ($value as $key2 => $value2) {
                    $dataAverage[$dateFormat][$key] = $dataAverage[$dateFormat][$key] ?? array();
                    $dataAverage[$dateFormat][$key][$key2] = $dataAverage[$dateFormat][$key][$key2] ?? 0;
                    $dataAverage[$dateFormat][$key][$key2] += $value2;
                }
            } else {
                $dataAverage[$dateFormat][$key] = $dataAverage[$dateFormat][$key] ?? 0;
                $dataAverage[$dateFormat][$key] += $value;
            }
        }

        $dataAverage[$dateFormat]['count'] = $dataAverage[$dateFormat]['count'] ?? 0;
        $dataAverage[$dateFormat]['count']++;
    }

    foreach ($dataAverage as $dateFormat => $values) {
        $count = $values['count'];
        foreach ($values as $key => $value) {
            if ($status == true && $key != 'count') {
                foreach ($value as $key2 => $value2) {
                    $dataAverage[$dateFormat][$key][$key2] = round($value2 / $count);
                }
            } else {
                $dataAverage[$dateFormat][$key] = round($value / $count);
            }
        }
        unset($dataAverage[$dateFormat]['count']);
    }

    ksort($dataAverage);
    return $dataAverage;
}

?>
<div class="statistics-section">
    <div class="plus-box">
        <h2>Total Hashrate</h2>
        <div class="statisticsOptions">
            <label for="timeIntervalHashrate">Interval:</label>
            <select id="timeIntervalHashrate" class="plus-btn">
                <option value="hourly">Hourly</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        <div>
            <canvas id="chartHashrate"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Extra</h2>
        <div class="statisticsOptions">
            <label for="timeIntervalExtra">Interval:</label>
            <select id="timeIntervalExtra" class="plus-btn">
                <option value="hourly">Hourly</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        <div>
            <canvas id="chartExtra"></canvas>
        </div>
    </div>
    <div class="plus-box">
        <h2>Total Algorithms</h2>
        <div class="statisticsOptions">
            <label for="chooseOnlineOffline">Miners:</label>
            <select id="chooseOnlineOffline" class="plus-btn">
                <option value="online">Mining</option>
                <option value="offline">Offline</option>
            </select>
            <br>
            <label for="timeIntervalOnlineOffline">Interval:</label>
            <select id="timeIntervalOnlineOffline" class="plus-btn">
                <option value="hourly">Hourly</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        <div>
            <canvas id="chartOnlineOffline"></canvas>
        </div>
    </div>
</div>

<script type="text/javascript" nonce="<?= $csp_nonce ?>">
    var myCharts = [];

    myCharts['Hashrate'] = null
    myCharts['Extra'] = null
    myCharts['OnlineOffline'] = null

    var statsData = []

    statsData['Hashrate'] = <?php echo json_encode($statsFullDataHashrate) ?>;
    statsData['Extra'] = <?php echo json_encode($statsFullDataExtra) ?>;
    statsData['OnlineOffline'] = <?php echo json_encode($statsFullDataOnlineOffline) ?>;

    localStorageCreator('Hashrate', statsData['Hashrate']['hourly'])
    localStorageCreator('Extra', statsData['Extra']['hourly'])
    localStorageCreator('OnlineOffline', statsData['OnlineOffline']['hourly'])

    document.getElementById('timeIntervalHashrate').addEventListener('change', function () { createChartStats('Hashrate'); }, false);
    document.getElementById('timeIntervalExtra').addEventListener('change', function () { createChartStats('Extra'); }, false);
    document.getElementById('timeIntervalOnlineOffline').addEventListener('change', function () { createChartStats('OnlineOffline'); }, false);
    document.getElementById('chooseOnlineOffline').addEventListener('change', function () { createChartStats('OnlineOffline'); }, false);

    createChartStats('Hashrate')
    createChartStats('Extra')
    createChartStats('OnlineOffline')

    function createChartStats(name) {
        var selectedChoose = undefined;
        var selectedInterval = document.getElementById('timeInterval' + name).value;
        var localStorageItems = JSON.parse(localStorage.getItem('config' + name));
        localStorageItems['defaultInterval'] = selectedInterval

        if (name == "OnlineOffline") {
            var selectedChoose = document.getElementById('choose' + name).value;

            localStorageItems['defaultChoose'] = selectedChoose
        }

        localStorage.setItem('config' + name, JSON.stringify(localStorageItems));

        data = statsData[name][selectedInterval]

        canvas = document.getElementById('chart' + name);

        var ticks = {
            color: "white"
        }

        if (name == "Hashrate") {
            var ticks = {
                color: "white",
                callback: function (value, index, values) {
                    // Format Hashrate
                    if (value >= 1000000000000) {
                        return (value / 1000000000000).toFixed(1) + ' TH/s';
                    } else if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + ' GH/s';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + ' MH/s';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + ' KH/s';
                    } else {
                        return value.toFixed(1) + ' H/s';
                    }
                }
            }
        }


        var labels = Object.keys(data);
        var datasets = [];
        var keys = [];

        for (var date in labels) {
            for (var miner in data[labels[date]]) {
                for (var key in data[labels[date]]) {
                    if (selectedChoose) {
                        for (var key2 in data[labels[date]][key]) {
                            keys[key2] = keys[key2] ?? 0;
                        }
                    }
                    else {
                        keys[key] = keys[key] ?? 0;
                    }
                }
            }
        }

        for (var key in keys) {
            if (selectedChoose == "online") {
                var dataPoints = labels.map(function (label) {
                    return data[label]['Online Miners']?.[key] ?? 0;
                });
            }
            else if (selectedChoose == "offline") {
                var dataPoints = labels.map(function (label) {
                    return data[label]['Offline Miners']?.[key] ?? 0;
                });
            }
            else {
                var dataPoints = labels.map(function (label) {
                    return data[label][key] ?? 0;
                });
            }

            var storageItem = JSON.parse(localStorage.getItem('config' + name));

            datasets.push({
                label: key,
                data: dataPoints,
                fill: false,
                hidden: storageItem ? storageItem['Legends'][key] : null
            });
        }

        if (myCharts[name] != null) {
            myCharts[name].clear();
            myCharts[name].destroy();
        }

        myCharts[name] = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: "white",
                        },
                        onClick: function (e, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            if (ci.isDatasetVisible(index)) {
                                ci.hide(index);
                                legendItem.hidden = true;
                            } else {
                                ci.show(index);
                                legendItem.hidden = false;
                            }

                            var storageItem = JSON.parse(localStorage.getItem('config' + name));

                            storageItem['Legends'][legendItem.text] = legendItem.hidden

                            localStorage.setItem('config' + name, JSON.stringify(storageItem));
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: "white",
                            autoSkip: true,
                            maxRotation: 0
                        }
                    },
                    y: {
                        min: 0,
                        ticks: ticks
                    }
                },
            },
        });
    }

    function localStorageCreator(name, data) {
        var storageItem = localStorage.getItem('config' + name);

        if (storageItem) {
            try {
                storageItem = JSON.parse(storageItem)
            }
            catch {
                storageItem = {}
            }
        }
        else {
            storageItem = {}
        }

        if (!(storageItem['defaultInterval'] !== undefined)) {
            storageItem['defaultInterval'] = document.getElementById('timeInterval' + name).value;
        }

        document.getElementById('timeInterval' + name).value = storageItem['defaultInterval'];

        if (name == 'OnlineOffline') {
            if (!(storageItem['defaultChoose'] !== undefined)) {
                storageItem['defaultChoose'] = document.getElementById('choose' + name).value;
            }

            document.getElementById('choose' + name).value = storageItem['defaultChoose'];
        }

        storageItem['Legends'] = storageItem['Legends'] || {};

        for (let date in data) {
            for (let label in data[date]) {
                if (!(storageItem['Legends'][label] !== undefined)) {
                    storageItem['Legends'][label] = null
                }
            }
        }

        localStorage.setItem('config' + name, JSON.stringify(storageItem));
    }
</script>
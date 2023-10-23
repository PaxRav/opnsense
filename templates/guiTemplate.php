<?php
use OCP\Util;
$appId = OCA\OPNsense\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-main');
Util::addStyle($appId, 'main');
?>

<div id="app-content">
<?php
if ($_['app_version']) {
    // you can get the values you injected as template parameters in the "$_" array
    echo '<h3>Cat Gif app version: ' . $_['app_version'] . '</h3>';
}
?>
    <div id="opnsense"></div>
</div>
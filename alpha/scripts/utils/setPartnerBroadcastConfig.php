<?php
if($argc < 3 || !is_numeric($argv[1]) || !file_exists($argv[2]))
{
	die('Usage: 
	php ' . __FILE__ . ' partner-id config-file-path
	
Configuration ini file may contain any of broadcast.ini values, or subset of them.
Example ini file:
' . file_get_contents(__DIR__ . '/../../../configurations/broadcast.ini'));
}

require_once (dirname(__FILE__) . '/../bootstrap.php');

$realRun = (isset($argv[3]) && $argv[3] === 'realRun');
KalturaStatement::setDryRun(!$realRun);

$partner = PartnerPeer::retrieveByPK($argv[1]);
if(!$partner)
{
	die('Partner [' . $argv[1] . '] not found');
}

$config = parse_ini_file($argv[2], true);
foreach($config as $key => $value)
{
	$partner->setLiveStreamBroadcastUrlConfigurations($key, $value);
}
$partner->save();

echo "Done.";


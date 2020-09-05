<?php

if ($argc > 1 && in_array($argv[1], array('--help', '-help', '-h', '-?')))
{
	echo "Usage:\n\t" . basename(__file__) . " <dryRun> <handleUnusedIndices> <sectionPrefix>\n";
	die;
}

$dryRun = false;
$sectionPrefix = null;
$handleUnusedIndices = false;

if($argc > 1)
{
	$dryRun = $argv[1];
}

if($argc > 2)
{
	$handleUnusedIndices = $argv[2];
}

if($argc > 3)
{
	$sectionPrefix = $argv[3];
}

chdir(dirname(__FILE__));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/elastic/' . basename(__FILE__) . '.cache');
KAutoloader::register();
error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$beaconElasticConfig = kConf::getMap('beacon_rotation');
foreach ($beaconElasticConfig as $sectionName => $configSection)
{
	if($sectionPrefix && !kString::beginsWith($sectionName, $sectionPrefix))
	{
		KalturaLog::log("$sectionName does not start with $sectionPrefix, skipping");
		continue;
	}

	$rotationWorker = new BeaconsIndexesRotationWorker($configSection, $dryRun, $handleUnusedIndices);
	$rotationWorker->rotate();
}

KalturaLog::log("Done!");

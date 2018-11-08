<?php


if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <config path>\n";
	die;
}

$configPath = $argv[1];
if (!file_exists($configPath))
{
	die("Config file [$configPath] doesn't exists\n");
}

chdir(dirname(__FILE__));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../../../'));
require_once(ROOT_DIR . '/infra/KAutoloader.php');
require_once(ROOT_DIR . '/alpha/config/kConf.php');
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/elastic/' . basename(__FILE__) . '.cache');
KAutoloader::register();
error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dryRun = false;
$configSections = parse_ini_file($configPath, true);
foreach ($configSections as $configSection)
{
	$rotationWorker = new ElasticIndexRotationWorker($configSection, $dryRun);
	$rotationWorker->rotate();	
}

KalturaLog::log("Done!");

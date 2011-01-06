<?php

define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
require_once(KALTURA_ROOT_PATH . '/infra/bootstrap_base.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

define("KALTURA_API_PATH", KALTURA_ROOT_PATH . "/api_v3");

// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/plugins/classMap.cache');
//KAutoloader::dumpExtra();
KAutoloader::register();

// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$entryId = '0_4c6o03wp';

if(isset($argv[1]))
	$entryId = $argv[1];

$providerData = new KalturaHuluDistributionJobProviderData();
$providerData->csId = 'Fox Sports';
$providerData->source = 'FOX_big12';
$providerData->metadataProfileId = 36;
$providerData->movFlavorAssetId = '0_vo1lfb5n';
$providerData->flvFlavorAssetId = '0_qz9lzewf';
$providerData->wmvFlavorAssetId = '0_qz9lzewf';
$providerData->thumbAssetId = '0_1e6adevn';

foreach($argv as $arg)
{
	$matches = null;
	if(preg_match('/(.*)=(.*)/', $arg, $matches))
	{
		$field = $matches[1];
		$providerData->$field = $matches[2];
	}
}

$data = new KalturaDistributionSubmitJobData();
$data->providerData = $providerData; 
$data->distributionProfile = new KalturaHuluDistributionProfile();
$data->distributionProfile->username = 'aaa';

$entry = entryPeer::retrieveByPKNoFilter($entryId);
$mrss = kMrssManager::getEntryMrss($entry);
file_put_contents('mrss.xml', $mrss);
KalturaLog::debug("MRSS [$mrss]");
		
$xml = HuluDistributionProvider::generateSubmitXML($entryId, $providerData);
$providerData->xml = $xml;
file_put_contents('out.xml', $xml);
KalturaLog::debug("XML [$xml]");

//$xml = file_get_contents('example.xml');
$providerData->xml = $xml;

$engine = new HuluDistributionEngine();
$engine->submit($data);

var_dump($data->remoteId);
KalturaLog::debug("remoteId [$data->remoteId]");

//$data->remoteId = '56ee9481-6463-42fa-b9b7-e784d73bd514';
$engine->closeSubmit($data);

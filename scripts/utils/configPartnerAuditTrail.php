<?php

$partnerId = 1;

set_time_limit(0);
ini_set("memory_limit","700M");
error_reporting(E_ALL);
chdir(dirname(__FILE__));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "audit", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();


$objectsToTrack = array(
	'entry' => array(
		'actions' => array(
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			entryPeer::NAME,
			entryPeer::TYPE,
			entryPeer::STATUS,
			'width', 
			'height'
		),
	),
	'flavorAsset' => array(
		'actions' => array(
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::FILE_SYNC_CREATED,
		),
		'descriptors' => array(
			flavorAssetPeer::TAGS,
			flavorAssetPeer::VERSION,
			flavorAssetPeer::STATUS,
		),
	),
	'Metadata' => array(
		'actions' => array(
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::CREATED,
		),
		'descriptors' => array(
			MetadataPeer::VERSION,
			MetadataPeer::STATUS,
		),
	),
);

KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setPluginEnabled(AuditPlugin::PLUGIN_NAME, true);
$partner->save();

foreach($objectsToTrack as $objectType => $objectConfig)
{
	$actions = implode(',', $objectConfig['actions']);
	$descriptors = isset($objectConfig['descriptors']) ? implode(',', $objectConfig['descriptors']) : null;
	
	$auditTrailConfig = AuditTrailConfigPeer::retrieveByObjectType($objectType, $partnerId);
	if(!$auditTrailConfig)
	{
		$auditTrailConfig = new AuditTrailConfig();
		$auditTrailConfig->setPartnerId($partnerId);
	}
		
	$auditTrailConfig->setObjectType($objectType);
	$auditTrailConfig->setActions($actions);
	$auditTrailConfig->setDescriptors($descriptors);
	$auditTrailConfig->save();
}

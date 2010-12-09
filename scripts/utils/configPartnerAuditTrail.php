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
	KalturaAuditTrailObjectType::ACCESS_CONTROL => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			accessControlPeer::NAME,
			accessControlPeer::SITE_RESTRICT_TYPE,
			accessControlPeer::SITE_RESTRICT_LIST,
			accessControlPeer::COUNTRY_RESTRICT_TYPE,
			accessControlPeer::COUNTRY_RESTRICT_LIST,
			accessControlPeer::KS_RESTRICT_PRIVILEGE,
			accessControlPeer::PRV_RESTRICT_PRIVILEGE,
			accessControlPeer::PRV_RESTRICT_LENGTH,
			accessControlPeer::KDIR_RESTRICT_TYPE,
		),
	),
	// admin_kuser is deperecated
	KalturaAuditTrailObjectType::ADMIN_KUSER => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			adminKuserPeer::FULL_NAME,
			adminKuserPeer::EMAIL,
			adminKuserPeer::LOGIN_BLOCKED_UNTIL,
		),
	),
	KalturaAuditTrailObjectType::CONVERSION_PROFILE_2 => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			conversionProfile2Peer::NAME,
		),
	),
	KalturaAuditTrailObjectType::ENTRY => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::COPIED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
			KalturaAuditTrailAction::VIEWED,
			KalturaAuditTrailAction::CONTENT_VIEWED,
			KalturaAuditTrailAction::RELATION_ADDED,
			KalturaAuditTrailAction::RELATION_REMOVED,
		),
		'descriptors' => array(
			entryPeer::NAME,
			entryPeer::TAGS,
			entryPeer::STATUS,
			entryPeer::LENGTH_IN_MSECS,
			entryPeer::PARTNER_DATA,
			entryPeer::ADMIN_TAGS,
			entryPeer::MODERATION_STATUS,
			entryPeer::PUSER_ID,
			entryPeer::ACCESS_CONTROL_ID,
			entryPeer::CONVERSION_PROFILE_ID,
			entryPeer::CATEGORIES,
			entryPeer::START_DATE,
			entryPeer::END_DATE,
			entryPeer::FLAVOR_PARAMS_IDS,
			entryPeer::AVAILABLE_FROM,
			"conversion_quality",
			"current_kshow_version",
			"encodingIP1",
			"encodingIP2",
			"streamUsername",
			"streamPassword",
			"streamRemoteId",
			"streamRemoteBackupId",
			"streamUrl",
			"streamBitrates",
			"ismVersion",
			"dynamicFlavorAttributes",
			"height",
			"width",
			"puserId",
			"thumb_offset",
		),
	),
	KalturaAuditTrailObjectType::FLAVOR_ASSET => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
			KalturaAuditTrailAction::VIEWED,
			KalturaAuditTrailAction::CONTENT_VIEWED,
			KalturaAuditTrailAction::RELATION_ADDED,
			KalturaAuditTrailAction::RELATION_REMOVED,
		),
		'descriptors' => array(
			flavorAssetPeer::TAGS,
			flavorAssetPeer::FLAVOR_PARAMS_ID,
			flavorAssetPeer::STATUS,
			flavorAssetPeer::VERSION,
			flavorAssetPeer::WIDTH,
			flavorAssetPeer::HEIGHT,
			flavorAssetPeer::BITRATE,
			flavorAssetPeer::FRAME_RATE,
			flavorAssetPeer::SIZE,
			flavorAssetPeer::FILE_EXT,
			flavorAssetPeer::CONTAINER_FORMAT,
			flavorAssetPeer::VIDEO_CODEC_ID,
		),
	),
	KalturaAuditTrailObjectType::FLAVOR_PARAMS_CONVERSION_PROFILE => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			flavorParamsConversionProfilePeer::READY_BEHAVIOR,
			flavorParamsConversionProfilePeer::FORCE_NONE_COMPLIED,
		),
	),
	KalturaAuditTrailObjectType::KSHOW_KUSER => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			KshowKuserPeer::SUBSCRIPTION_TYPE,
			KshowKuserPeer::ALERT_TYPE,
		),
	),
	KalturaAuditTrailObjectType::MEDIA_INFO => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
		),
		'descriptors' => array(
		),
	),
	KalturaAuditTrailObjectType::PARTNER => array(
		'actions' => array(
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			PartnerPeer::PARTNER_NAME,
			PartnerPeer::URL1,
			PartnerPeer::URL2,
			PartnerPeer::ADMIN_NAME,
			PartnerPeer::ADMIN_EMAIL,
			PartnerPeer::NOTIFY,
			PartnerPeer::STATUS,
			PartnerPeer::ADULT_CONTENT,
			"allowQuickEdit",
			"defConversionProfileType",
			"curConvProfType",
			"defaultAccessControlId",
			"defaultConversionProfileId",
			"notificationsConfig",
			"allowMultiNotification",
			"defThumbOffset",
			"host",
			"cdnHost",
			"forceCdnHost",
			"rtmpUrl",
			"iisHost",
			"landingPage",
			"userLandingPage",
		),
	),
	KalturaAuditTrailObjectType::METADATA => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			MetadataPeer::VERSION,
			MetadataPeer::STATUS,
		),
	),
	KalturaAuditTrailObjectType::METADATA_PROFILE => array(
		'actions' => array(
			KalturaAuditTrailAction::CREATED,
			KalturaAuditTrailAction::CHANGED,
			KalturaAuditTrailAction::DELETED,
		),
		'descriptors' => array(
			MetadataProfilePeer::VERSION,
			MetadataProfilePeer::STATUS,
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

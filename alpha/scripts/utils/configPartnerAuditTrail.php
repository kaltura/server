<?php

$partnerId = 1;

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$objectsToTrack = array(
	AuditTrailObjectType::ACCESS_CONTROL => array(
		'actions' => array(
			'CREATED',
			'CHANGED',
			'DELETED',
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
	AuditTrailObjectType::CONVERSION_PROFILE_2 => array(
		'actions' => array(
			'CREATED',
			'CHANGED',
			'DELETED',
		),
		'descriptors' => array(
			conversionProfile2Peer::NAME,
		),
	),
	AuditTrailObjectType::ENTRY => array(
		'actions' => array(
			'CREATED',
			'CHANGED',
			'DELETED',
			'VIEWED',
			'CONTENT_VIEWED',
			'RELATION_ADDED',
			'RELATION_REMOVED',
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
	AuditTrailObjectType::FLAVOR_ASSET => array(
	'actions' => array(
		'CREATED',
		'CHANGED',
		'DELETED',
		'VIEWED',
		'CONTENT_VIEWED',
		'RELATION_ADDED',
		'RELATION_REMOVED',
	),
	'descriptors' => array(
		assetPeer::TAGS,
		assetPeer::FLAVOR_PARAMS_ID,
		assetPeer::STATUS,
		assetPeer::VERSION,
		assetPeer::WIDTH,
		assetPeer::HEIGHT,
		assetPeer::BITRATE,
		assetPeer::FRAME_RATE,
		assetPeer::SIZE,
		assetPeer::FILE_EXT,
		assetPeer::CONTAINER_FORMAT,
		assetPeer::VIDEO_CODEC_ID,
	),
),
	AuditTrailObjectType::FLAVOR_PARAMS_CONVERSION_PROFILE => array(
	'actions' => array(
		'CREATED',
		'CHANGED',
		'DELETED',
	),
	'descriptors' => array(
		flavorParamsConversionProfilePeer::READY_BEHAVIOR,
		flavorParamsConversionProfilePeer::FORCE_NONE_COMPLIED,
	),
),
	AuditTrailObjectType::KSHOW_KUSER => array(
	'actions' => array(
		'CREATED',
		'CHANGED',
		'DELETED',
	),
	'descriptors' => array(
		KshowKuserPeer::SUBSCRIPTION_TYPE,
		KshowKuserPeer::ALERT_TYPE,
	),
),
	AuditTrailObjectType::MEDIA_INFO => array(
	'actions' => array(
		'CREATED',
	),
	'descriptors' => array(
	),
),
	AuditTrailObjectType::PARTNER => array(
	'actions' => array(
		'CHANGED',
		'DELETED',
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
		"forceCdnHost",
		"rtmpUrl",
		"iisHost",
		"landingPage",
		"userLandingPage",
	),
),
	AuditTrailObjectType::METADATA => array(
	'actions' => array(
		'CREATED',
		'CHANGED',
		'DELETED',
	),
	'descriptors' => array(
		MetadataPeer::VERSION,
		MetadataPeer::STATUS,
	),
),
	AuditTrailObjectType::METADATA_PROFILE => array(
	'actions' => array(
		'CREATED',
		'CHANGED',
		'DELETED',
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

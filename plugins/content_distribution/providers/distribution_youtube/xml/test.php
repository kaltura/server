<?php

define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
require_once(KALTURA_ROOT_PATH . '/infra/bootstrap_base.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'alpha'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'kConf.php');
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

kCurrentContext::$ps_vesion = 'ps3';

$timestampName = date('Ymd-His') . '_' . time();
$metadataTempFileName = 'youtube_' . $timestampName . '.xml';
$notificationEmail = 'roman.kreichman@kaltura.com';
$userName = 'kalturasandbox';
$title = 'my title 2';
$description = 'my description';
$serverUrl = 'foxsports-kaltura.xfer.youtube.com';
$loginName = 'foxsports-kaltura';
$publicKeyFile = '/var/www/kaltura/app/plugins/distribution_youtube/id_rsa_youtube.pub';
$privateKeyFile = '/var/www/kaltura/app/plugins/distribution_youtube/id_rsa_youtube';
$metadataTemplate = '/var/www/kaltura/app/plugins/distribution_youtube/xml/metadata_template.xml';
$videoFileFile = '/var/www/kaltura/app/plugins/distribution_youtube/xml/Logo_White.flv';
$deliveryCompleteFile = '/var/www/kaltura/app/plugins/distribution_youtube/xml/delivery.complete';

$metadataTempFilePath = kConf::get('temp_folder') . '/distribution/';
if (!file_exists($metadataTempFilePath))
	mkdir($metadataTempFilePath);
$metadataTempFilePath = $metadataTempFilePath . $metadataTempFileName;

// prepare the metadata
$doc = new DOMDocument();
$doc->load($metadataTemplate);

$xpath = new DOMXPath($doc);
$xpath->registerNamespace('media', 'http://search.yahoo.com/mrss');
$xpath->registerNamespace('yt', 'http://www.youtube.com/schemas/yt/0.2');

$notificationEmailNode = $xpath->query('/rss/channel/yt:notification_email')->item(0);
$userNameNode = $xpath->query('/rss/channel/yt:account/yt:username')->item(0);
$titleNode = $xpath->query('/rss/channel/item/media:title')->item(0)->childNodes->item(0);
$descriptionNode = $xpath->query('/rss/channel/item/media:content/media:description')->item(0)->childNodes->item(0);
$fileNameNode = $xpath->query('/rss/channel/item/media:content/@url')->item(0);

$notificationEmailNode->nodeValue = $notificationEmail;
$userNameNode->nodeValue = $userName;
$titleNode->nodeValue = $title;
$descriptionNode->nodeValue = $description;
$fileNameNode->nodeValue = 'file://' . pathinfo($videoFileFile, PATHINFO_BASENAME);

$doc->save($metadataTempFilePath);

// open connection
$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
$fileTransferMgr->loginPubKey($serverUrl, $loginName, $publicKeyFile, $privateKeyFile);

$directoryName = '/' . $timestampName;

// upload the metadata
$fileTransferMgr->putFile($directoryName . '/' . $metadataTempFileName, $metadataTempFilePath);

// upload the video
$fileTransferMgr->putFile($directoryName . '/' . pathinfo($videoFileFile, PATHINFO_BASENAME), $videoFileFile);

// upload the delivery.complete marker file
$fileTransferMgr->putFile($directoryName . '/' . pathinfo($deliveryCompleteFile, PATHINFO_BASENAME), $deliveryCompleteFile);
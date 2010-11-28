<?php

ini_set("memory_limit","1024M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$thumbParams = new thumbParams();
$thumbParams->setVersion(1);
$thumbParams->setPartnerId(0);
$thumbParams->setTags('');
$thumbParams->setIsDefault(false);
$thumbParams->setFormat(thumbParams::CONTAINER_FORMAT_JPG);
//$thumbParams->setWidth(800);
//$thumbParams->setHeight(600);

$thumbParams->setSourceParamsId(3);
$thumbParams->setCropType(1);
//$thumbParams->setQuality(100);
$thumbParams->setCropX(100);
$thumbParams->setCropY(100);
//$thumbParams->setCropWidth();
//$thumbParams->setCropHeight();
//$thumbParams->setCropProvider();
//$thumbParams->setCropProviderData();
$thumbParams->setVideoOffset(2);
//$thumbParams->setScaleWidth();
//$thumbParams->setScaleHeight();
//$thumbParams->setBackgroundColor();

$thumbParams->setName($thumbParams->getWidth() . ' x ' . $thumbParams->getHeight());
$thumbParams->setDescription($thumbParams->getName());

$thumbParams->save();

echo "Done\n";

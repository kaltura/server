<?php

$partner_id = @$argv[1];
if(is_null($partner_id)) die('must supply partner ID'.PHP_EOL);

ini_set("memory_limit","256M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
//KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner) die('could not load partner '.$partner_id.PHP_EOL);

if(version_compare($partner->getKmcVersion(), '3') < 0) die('partner is not set to KMC3. need to upgrade first'.PHP_EOL);

if($partner->getEnableVast()) echo 'vast already enabled for partner'.PHP_EOL;
else
{
	$partner->setEnableVast(1);
	$partner->save();
	echo 'partner was enabled vast'.PHP_EOL;
}


PartnerPeer::resetPartnerInCache ( $partner_id );

<?php
define ('DEBUG', false);
require_once ( "./define.php" );

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/myContentStorage.class.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");

require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();
$partner_id = @$argv[1];

$should_do_flavors_to_web = @$argv[2];
$force_upgrade = '';
$force_upgrade = @$argv[2];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
	die('no such partner.'.PHP_EOL);
}

if($partner->getKmcVersion() == '1')
{
	echo 'Partner is pre-andromeda. upgrade to andromeda first'.PHP_EOL;
	die;
}
if($partner->getKmcVersion() == '3')
{
	echo 'Partner is already on butterfly KMC'.PHP_EOL;
	die;
}

$partner->setKmcVersion('3');
if(@$argv[2] == 'live')
{
	$partner->setLiveStreamEnabled(1);
}

// TODO - add silverlight flag option
if(@$argv[2] == 'slp' || (@$argv[2] == 'live' && @$argv[3] == 'slp'))
{
	// set enable SLP
}
$partner->save();
exit();


<?php

ini_set("memory_limit","256M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

if (count($argv) !== 1)
{
	die('please provide a partner id' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}
$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
	die('no such partner.'.PHP_EOL);
}
$partner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
$partner->save();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
$c->setLimit(20);

$entries = entryPeer::doSelect($c);
while(count($entries))
{
	foreach($entries as $entry)
	{
		$entry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
		$entry->save();	
	}
	$entries = entryPeer::doSelect($c);
}

echo 'Done';

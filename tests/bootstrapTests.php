<?php
if (!defined("KALTURA_ROOT_PATH"))
{
	require_once(dirname(__FILE__) . "/helpers/KalturaTestsAutoload.php"); 
	require_once("bootstrap.php");
	KAutoloader::register();
	KalturaTestsAutoload::register();
	
	$dbManager = new DbManager();
	$configPath = realpath(KALTURA_API_PATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.ini");
	$dbManager->setConfig(new Zend_Config_Ini($configPath));
	$dbManager->initialize();
}
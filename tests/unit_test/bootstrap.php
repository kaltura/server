<?php 

//TODO:Fix this and the cache

//API bootstrap
require_once (dirname(__FILE__). '/../../api_v3/bootstrap.php');

//The kaltura client
require_once (dirname(__FILE__). '/lib/kalturaClient.php');

require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

define("KALTURA_UNIT_TEST_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."unit_test");

//Unit Test Project files (used in the zend phpunit library notneeded for the command line)
require_once ('/PHPUnit/Framework.php');

//Absolute path with a var (good for all systems as we are using KALTURA_ROOT_PATH)
require_once (dirname(__FILE__). '/unitTestDataGenerator/unitTestDataGenerator.php');
require_once (dirname(__FILE__). '/infra/unitTestData.php');
require_once (dirname(__FILE__). '/infra/unitTestBase.php');

// Autoloader
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "config", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
Kautoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(KAutoloader::buildPath(KALTURA_UNIT_TEST_PATH, "cache", "KalturaClassMap.cache"));
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

//Set the DB
DbManager::setConfig(kConf::getDB());
DbManager::initialize();



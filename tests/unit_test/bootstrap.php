<?php 

//TODO: Fix this shit. move the cache localy or discard it.
require_once(dirname(__FILE__).'/../../alpha/config/sfrootdir.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."../..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");

//TODO:Fix this adn the cache
define("KALTURA_UNIT_TEST_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."unit_test");

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

//Unit Test Project files
require_once ('tests/unit_test/unitTestDataGenerator/unitTestDataGenerator.php');require_once ('tests/unit_test/infra/unitTestBase.php');

?>
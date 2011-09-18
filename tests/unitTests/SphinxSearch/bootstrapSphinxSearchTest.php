<?php

ini_set ( "memory_limit", "1024M" );
error_reporting ( E_ALL );

$GLOBALS ["sphinxTest"] = true;

define ( 'ROOT_DIR', dirname(__FILE__) . "/../../../" );
define ( 'SPHINX_SEARCH_API_SESSION', 'testsData/SphinxSearchApiSession.csv' );
define ( 'KALTURA_API_LOGGER_FILE_PATH', ROOT_DIR . "/api_v3/config/logger.ini");

define ( 'ARRAY_SIZE', 3 );
define ( 'SELECT_INDEX', 2 );

require_once (ROOT_DIR . '/infra/bootstrap_base.php');
require_once (ROOT_DIR . '/infra/KAutoloader.php');
require_once (ROOT_DIR . '/infra/kConf.php');

KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "alpha", "*" ) );
KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "infra", "*" ) );
KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "vendor", "*" ) );
KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "plugins", "*" ) );
KAutoloader::setClassMapFilePath ( 'cache/classMap.cache' );
KAutoloader::register ();

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();
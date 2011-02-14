<?php

require_once (dirname ( __FILE__ ) . "/../../infra/bootstrap_base.php");
require_once (KALTURA_ROOT_PATH . '/alpha/config/kConf.php');

// Autoloader - override the autoloader defaults
require_once (KALTURA_INFRA_PATH . DIRECTORY_SEPARATOR . "KAutoloader.php");

KAutoloader::setClassPath ( array( 	KAutoloader::buildPath ( KALTURA_ROOT_PATH, "vendor", "propel", "*" ), // both 
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "infra", "*" ),  // both
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "alpha", "lib", "*" ), // server
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "plugins", "*" ),  // both 
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "tests", "base", "*" ), 
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "tests", "lib", "*" ), //Client
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "tests", "UnitTests", "*" ), 
					KAutoloader::buildPath ( KALTURA_ROOT_PATH, "tests", "roles_and_permissions", "*" ), //TODO: move to the unitTest folder 
					));

//Add the zend phpunit support 
KAutoloader::setIncludePath ( array (KAutoloader::buildPath ( KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library" ) ) );

//File path to entire server cache folder
KAutoloader::setClassMapFilePath ( kConf::get ( "cache_root_path" ) . 'unitTest_clientSide/classMap.cache' );

KAutoloader::register ();

//The kaltura client
date_default_timezone_set ( kConf::get ( "date_default_timezone" ) ); // America/New_York


//Set the DB
DbManager::setConfig ( kConf::getDB () );
DbManager::initialize ();

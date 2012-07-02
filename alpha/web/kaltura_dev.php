<?php

require_once(dirname(__FILE__).'/../../infra/kConf.php');

require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// Logger
$loggerConfigPath = realpath(dirname(__FILE__) . "/../../configurations/logger.ini");
try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	$ps2 = $config->ps2_dev;
	KalturaLog::initLog($ps2);
	KalturaLog::setContext('PS2');
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}

sfLogger::getInstance()->registerLogger(KalturaLog::getInstance());
sfLogger::getInstance()->setLogLevel(7);
sfConfig::set('sf_logging_enabled', true);

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

sfContext::getInstance()->getController()->dispatch();

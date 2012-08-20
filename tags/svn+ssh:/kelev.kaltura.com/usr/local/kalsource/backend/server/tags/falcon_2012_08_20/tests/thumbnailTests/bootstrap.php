<?php

require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'infra' . DIRECTORY_SEPARATOR . 'bootstrap_base.php');
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'infra' . DIRECTORY_SEPARATOR . 'kConf.php');
//require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'alpha' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'kConf.php');
require_once (KALTURA_INFRA_PATH . DIRECTORY_SEPARATOR . 'KAutoloader.php');
KAutoloader::setClassMapFilePath(kConf::get('cache_root_path') . '/tests/thumbnail.classMap.cache');
KAutoloader::register ();

// Timezone
date_default_timezone_set(kConf::get('date_default_timezone')); // America/New_York

// Logger
$loggerConfigPath = realpath(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR . 'logger.ini');
try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	KalturaLog::initLog($config->tests);
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}



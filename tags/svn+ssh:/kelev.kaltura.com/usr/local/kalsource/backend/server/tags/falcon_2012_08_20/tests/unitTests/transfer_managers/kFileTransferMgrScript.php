<?php
error_reporting(E_ALL | E_WARNING);
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");

define("KALTURA_TESTS_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."tests");
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'infra'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "phpseclib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));

set_include_path(get_include_path() . PATH_SEPARATOR . KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "phpseclib"));

KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/tests/' . basename(__FILE__) . '.cache');
KAutoloader::register();

// Timezone
$timeZone = kConf::get("date_default_timezone");

$isTimeZone = substr_count($timeZone, '@') == 0; //no @ in a real time zone

if($isTimeZone)
	date_default_timezone_set($timeZone); // America/New_York

// Logger
$loggerConfigPath = KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'configurations'.DIRECTORY_SEPARATOR."logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	KalturaLog::initLog($config->tests);
	KalturaLog::setContext("tests");
}
catch(Zend_Config_Exception $ex)
{
}




//$server = 'hudsontest4.kaltura.dev';
//$user = 'root';
//$pass = '';
//$port = 22;
//$remote_path = '/root/anatol';
//$file = '0_5shsavmd.mpg';


//$server = 'hudsontest4.kaltura.dev';
//$user = 'test4';
//$pass = 'test4';
//$port = 22;
//$remote_path = '.';
//$file = 'sfToolkit.class.php';


//$server = 'hudsontest4.kaltura.dev';
//$user = 'test6';
//$certificate = 'cert/id_rsa';
//$port = 22;
//$remote_path = '.';
//$file = 'issue';


$server = 'hudsontest4.kaltura.dev';
$user = 'test7';
$certificate = 'cert/id_rsa';
$port = 22;
$remote_path = '.';
$file = 'issue';

try
{
	$kFileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
//	$kFileTransferMgr->login($server, $user, $pass, $port);
	$kFileTransferMgr->loginPubKey($server, $user, null, $certificate, null, $port);
			
	$list = $kFileTransferMgr->listDir("$remote_path");
	var_dump($list);

	$actualSize = $kFileTransferMgr->fileSize("$remote_path/$file");
	echo "size: $actualSize\n";
}
catch(Exception $e)
{
	echo "error: " . $e->getMessage() . "\n";
}

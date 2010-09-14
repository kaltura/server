<?php

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

include('migrateConversionProfiles.php');

ini_set("memory_limit","512M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$offset = 0;
$bulk = 500;
$start_partner = @$argv[1];
$stop_partner = @$argv[2];
while(1)
{
	$c = new Criteria();
	$c->setOffset($offset);
	$c->setLimit($bulk);
	if($start_partner)
		$c->addAnd(PartnerPeer::ID, $start_partner, Criteria::GREATER_EQUAL);

	if($stop_partner)
		$c->addAnd(PartnerPeer::ID, $stop_partner, Criteria::LESS_EQUAL);

	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$partners = PartnerPeer::doSelect($c);
	$offset += $bulk;
	if(!count($partners))
		break;

	foreach($partners as $partner)
	{
		AndromedaMigration::initScript($partner);
		AndromedaMigration::domigrate($partner);
	}
}
exit();
exit(0);

class AndromedaMigration
{
	private static $partnerLog;
	public static function initScript($partner)
	{
		$partner_prefix = (int)($partner->getId()/1000);
		$logFile_dir = 'logs/'.$partner_prefix.'/';
		$partner_log_file = $logFile_dir.$partner->getId().'_convProfs.log';
		
		if(!file_exists($partner_log_file))
		{
			kFile::fullMkdir($partner_log_file);
		}
		if(!file_exists($partner_log_file))
		{
			$f = fopen($partner_log_file, 'x') or die('could not create partner log file at '.$partner_log_file);
			fclose($f);
		}
		$logWriter = new Zend_Log_Writer_Stream($partner_log_file);
		self::$partnerLog = new Zend_Log($logWriter);
	}
	private static $offset = 0;
	private static $bulk = 100;
	
	public static function domigrate($partner)
	{
		self::$offset = 0;
		while(1)
		{
			$c = new Criteria();
			$c->addAnd(ConversionProfilePeer::PARTNER_ID, $partner->getId());
			$c->addAscendingOrderByColumn(ConversionProfilePeer::CREATED_AT);
			$c->setLimit(self::$bulk);
			$c->setOffset(self::$offset);
			$confs = ConversionProfilePeer::doSelect($c);

			self::$offset += self::$bulk;
			if(!count($confs))
				break;
			
			$failedIds = array();
		
			self::logPartner("     looping from ".self::$offset.", limit ".self::$bulk); 
			$res = migrateConversionProfiles::migrateConversionProfileList($confs, $partner);
			if($res == 0 || $res == 2)
			{
				$failedIds[$offset] = migrateConversionProfiles::getFailedIds();
			}
			else
			{
			}
			
			$failed_ids = implode(',', $failedIds);
			self::logPartner(" bulk started from ".self::$offset." ; bulk size is: ".self::$bulk." ; falied ids: $failed_ids");

		}
	}
	public static function logPartner($msg, $priority = Zend_Log::INFO)
	{
		self::$partnerLog->log("[[".memory_get_usage()."]] ".$msg, $priority);
	}
}
	

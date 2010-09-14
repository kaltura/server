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

include('migrateUiconfs.php');

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$partner_id = @$argv[1];
$loopIndex = @$argv[2];
$loopSize = @$argv[3];
$work_from_date = @$argv[4];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner) exit(1);

AndromedaMigration::initScript($partner);
if($loopIndex == 'single')
{
	$uiconfId = @$argv[3];
	$conf = uiConfPeer::retrieveByPK($uiconfId);
	migrateUiconfs::migrateSingleUiconf($conf);
}
else
{
	AndromedaMigration::douiconfs($partner, $work_from_date, $loopSize, $loopIndex);
}
exit(0);

class AndromedaMigration
{
	private static $partnerLog;
	public static function initScript($partner)
	{
		$partner_prefix = (int)($partner->getId()/1000);
		$logFile_dir = 'logs/'.$partner_prefix.'/';
		$partner_log_file = $logFile_dir.$partner->getId().'_entries.log';
		
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
	
	public static function douiconfs($partner, $work_from_date, $loopSize, $loopIndex)
	{
		$c = new Criteria();
		$c->addAnd(uiConfPeer::PARTNER_ID, $partner->getId());
		if($work_from_date)
		{
			$c->addAnd(uiConfPeer::UPDATED_AT, $work_from_date, Criteria::GREATER_EQUAL);
		}	
		$c->addAscendingOrderByColumn(uiConfPeer::CREATED_AT);
		$c->setLimit($loopSize);
		$failedIds = array();
		$c->setOffset($loopIndex);
		$entries = uiConfPeer::doSelect($c);
		
		if(!$entries || count($entries) == 0)
		{
			exit(1);
		}
		
		self::logPartner("     looping from $loopIndex, limit $loopSize [work_from_date is $work_from_date]");
		$res = migrateUiconfs::migrateUiconfList($entries, $partner);
		if($res == 0 || $res == 2)
		{
			$failedIds[$loopIndex] = migrateUiconfs::getFailedIds();
		}
		else
		{
		}
		
		$failed_ids = implode(',', $failedIds);
		self::logPartner(" bulk started from $loopIndex ; bulk size is: $loopSize ; falied ids: $failed_ids");
	}
	public static function logPartner($msg, $priority = Zend_Log::INFO)
	{
		self::$partnerLog->log("[[".memory_get_usage()."]] ".$msg, $priority);
	}
}
	

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

include('migrateEntries.php');

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$entry_list = @$argv[1];
if(!$entry_list)
	die('not given entry list'.PHP_EOL);

AndromedaMigration::initScript();
AndromedaMigration::doEntries($entry_list);

exit(0);

class AndromedaMigration
{
	private static $partnerLog;
	public static function initScript()
	{
		$partner_prefix = '';
		$logFile_dir = 'logs/by_entry_list/';
		$partner_log_file = $logFile_dir.(time()).'_entries.log';
		
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
	
	public static function doEntries($entry_list)
	{
		self::logPartner('doing entries: '.$entry_list);

		$c = new Criteria();
		$c->addAnd(entryPeer::ID, explode(',', $entry_list), Criteria::IN);
		$failedIds = array();
		$entries = entryPeer::doSelect($c);

		self::logPartner('selected entries: '.count($entries));		
		if(!$entries || count($entries) == 0)
		{
			exit(1);
		}
		$res = null;
		$res = migrateEntries::migrateEntryList($entries);
		if($res == 0 || $res == 2)
		{
			$failedIds[] = migrateEntries::getFailedIds();
		}
		else
		{
		}
		if(is_array($failedIds) && count($failedIds))
		{
			$arrs = implode(',', $failedIds);
			if($arrs)
			{
				$failed_ids = implode(',', $arrs);
			}
		}
	}
	public static function logPartner($msg, $priority = Zend_Log::INFO)
	{
		self::$partnerLog->log("[[".memory_get_usage()."]] ".$msg, $priority);
	}
}
	

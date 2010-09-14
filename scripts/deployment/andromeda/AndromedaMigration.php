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
require_once('migrateUiconfs.php');
require_once('migrateConversionProfiles.php');
require_once('migrateEntries.php');
require_once('migrateBulkUploads.php');

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$partner_id = @$argv[1];
$starting_date = @$argv[2];

$start_partner_id = null;
$stop_partner_id = null;
if($partner_id == 'all')
{
	$start_partner_id = @$argv[3];
	$stop_partner_id = @$argv[4];
}

if($starting_date)
{
	// validate date format YYYY-MM-DD
	$date_parts = explode('-', $starting_date);
	if(count($date_parts) == 3 && strlen($date_parts[0]) == 4 && is_numeric($date_parts[0]) && is_numeric($date_parts[1]) &&
	   (int)$date_parts[1] < 13 && is_numeric($date_parts[2]) && (int)$date_parts[2] < 31 )
	{
		AndromedaMigration::setWorkFromDate($starting_date);
	}
	else
	{
		die('wrong date format. must be valid date in format YYYY-MM-DD'.PHP_EOL);
	}
}

if(is_null($partner_id))
{
	echo 'if you want to loop all partner, you have to mean it !'.PHP_EOL;
	echo 'don\'t expect me to work hard just because you forgot to provide partner ID'.PHP_EOL;
	echo PHP_EOL.'usage for looping all partner: '.PHP_EOL;
	echo 'php '.pathinfo(__FILE__, PATHINFO_FILENAME).' all [start-date YYYY-MM-DD]'.PHP_EOL;
	die();
}
elseif($partner_id == 'all')
{
	$script_start_time = microtime(true);
	$partners_processed = 0;
	while(true)
	{
		$partners = AndromedaMigration::loadBulkOfPartners($start_partner_id, $stop_partner_id);
		if($partners === FALSE)
		{
			echo 'No more partners'.PHP_EOL;
			break;
		}
		foreach($partners as $partner)
		{
			$partners_processed++;
			AndromedaMigration::migratePartner($partner);
		}
	}
	$script_end_time = microtime(true);
	echo PHP_EOL.'looped all partners. full process took: '.($script_end_time - $script_start_time).PHP_EOL;
	if($partners_processed > 0)
	{
		$topTimePartners = AndromedaMigration::getTopProcessPartners();
		echo 'each partner average: '.AndromedaMigration::calculateStatsAverage().PHP_EOL;
		echo 'Partners that took most time: '.PHP_EOL;
		foreach($topTimePartners as $pid => $time)
		{
			echo '     PartnerId: '.$pid.', time: '.$time.PHP_EOL;
		}
	}
}
else
{
	$partner = PartnerPeer::retrieveByPK($partner_id);
	if(!$partner) die ('could not load single partner '.$partner_id);
	AndromedaMigration::migratePartner($partner);
}
@unlink('./logs/classMap.cache');
exit();

class AndromedaMigration
{
	private static $work_from_date = null;
	
	public static function setWorkFromDate($v)
	{
		self::$work_from_date = $v;
	}
	
	const PARTNER_BULK_SIZE = 500;
	private static $partnerLoopIndex = 0;
	
	private static $partner_timing_stats;
	
	private static $partnerLog;
	private static $scriptLog;
	public static function loadBulkOfPartners($start_partner_id = null, $stop_partner_id = null)
	{
		$c = new Criteria();
		if($start_partner_id)
		{
			$c->addAnd(PartnerPeer::ID, $start_partner_id, Criteria::GREATER_EQUAL);
		}
		if($stop_partner_id)
		{
			$c->addAnd(PartnerPeer::ID, $stop_partner_id, Criteria::LESS_EQUAL);
		}
		$c->setOffset(self::$partnerLoopIndex);
		$c->setLimit(self::PARTNER_BULK_SIZE);
		$c->addAscendingOrderByColumn(PartnerPeer::ID);
		$partners = PartnerPeer::doSelect($c);
		self::$partnerLoopIndex = self::$partnerLoopIndex + self::PARTNER_BULK_SIZE;
		if(count($partners)) return $partners;
		return FALSE;
	}
	
	private static $partnerSummary = array();
	
	private static function initScript()
	{
		if(!isset(self::$scriptLog))
		{
			if(!file_exists('logs/migration.log'))
			{
				kFile::fullMkdir('logs/migration.log');
			}
			if(!file_exists('logs/migration.log'))
			{
				$f = fopen('logs/migration.log', 'x') or die('could not create script log file at '.'logs/migration.log');
				fclose($f);
			}
			$scriptWriter = new Zend_Log_Writer_Stream('logs/migration.log');
			
			self::$scriptLog = new Zend_Log($scriptWriter);
		}
		if(!self::$partner_timing_stats)
		{
			self::$partner_timing_stats = array();
		}
	}
	
	public static function getTopProcessPartners($count = 10)
	{
		$sorted_by_val = arsort(self::$partner_timing_stats);
		if($sorted_by_val)
		{
			return array_slice(self::$partner_timing_stats, 0, $count);
		}
	}
	
	public static function calculateStatsAverage()
	{
		$total_time = 0;
		foreach(self::$partner_timing_stats as $partnerId => $time)
		{
			$total_time += $time;
		}
		
		return ($total_time / count(self::$partner_timing_stats));
	}
	
	private static function initMigration($partner)
	{
		self::$partnerSummary = array();
		
		self::initScript();
		// context is the partnerId
		self::$scriptLog->setEventItem("context", $partner->getId());
		// master log - for all partners - summery
		// line 1 - starting partner + id
		// line 2 - summery of all actions
		self::logScript ( "------ START: Migrating partner {$partner->getId()} ", Zend_Log::INFO); //
		
		// log file per partner
		$partner_prefix = (int)($partner->getId()/1000);
		$logFile_dir = 'logs/'.$partner_prefix.'/';
		$partner_log_file = $logFile_dir.$partner->getId().'.log';
		
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
		self::$partnerLog->log('----------------------------------------------------------------------- '.$partner->getId(), Zend_Log::INFO);
		self::$partnerLog->log('Starting migration of partner '.$partner->getId(), Zend_Log::INFO);
	}
	
	public static function migratePartner(Partner $partner)
	{
		$start_time = microtime(true);
		self::initMigration($partner);
		
		
		if(!self::$work_from_date ||
		   (self::$work_from_date && strtotime($partner->getUpdatedAt()) > strtotime(self::$work_from_date)))
		{
			self::logPartner("partner's updated_at is after start date argument. doing actions on partner object.");
			$partnerUpdatedAt = strtotime($partner->getUpdatedAt())+1;
			$accessControlId = self::createDefaultAccessControl($partner);
			if($accessControlId)
				$partner->setDefaultAccessControlId($accessControlId);
	
			$conversionProfileId = self::createConversionProfile2($partner);
			if($conversionProfileId)
				$partner->getDefaultConversionProfileId($conversionProfileId);
			
			if($partner->getServiceConfigId() == 'services_block.ct')
			{
				$partner->setStatus(Partner::PARTNER_STATUS_FULL_BLOCK); // set partner status to full block
			}
			// make sure updated_at not changes
			$partner->setUpdatedAt ( $partnerUpdatedAt ) ;
			$partner->save();
		}
		else
		{
			self::logPartner("partner's updated_at is before start date argument. not doing actions on partner object.");
		}
		

		self::migratePartnerEntries($partner);
		self::migratePartnerOldConversionProfiles($partner);
		self::migratePartnerUiConfs($partner);
		self::migratePartnerBulkUploads($partner);

		// log the results (ids or count) to master log
		self::logScript ( "Migrating partner {$partner->getId()} summary: ".str_replace(PHP_EOL, '', print_r(self::$partnerSummary,true)), Zend_Log::INFO ); //
		$end_time = microtime(true);
		self::logScript ( "Partner benchmark - process took ".($end_time - $start_time)." seconds", Zend_Log::INFO );
		self::$partner_timing_stats[$partner->getId().' '.$partner->getPartnerName()] = $end_time - $start_time;
		self::logScript ( "------ FINISH: Migrating partner {$partner->getId()} ", Zend_Log::INFO); //
	}
	
	public static function createDefaultAccessControl($partner)
	{
		if($partner->getDefaultAccessControlId())
		{
			self::logPartner("I already have default AccessControl ID: ".$partner->getDefaultAccessControlId());
			self::$partnerSummary['AccessControlID'] = 'OK';
			return false; // we don't need to set anything on the partner
		}
		self::logPartner("I don't have default AccessControl ID, let's find one");
		// try to load default of partner
		$c = new Criteria();
		if(self::$work_from_date)
		{
			$c->addAnd(accessControlPeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}
		$c->addAnd(accessControlPeer::PARTNER_ID, $partner->getId());
		$count = accessControlPeer::doCount($c);
		// on success - return ID
		if($count > 0)
		{
			$c->addDescendingOrderByColumn(accessControlPeer::CREATED_AT);
			$accessControl = accessControlPeer::doSelectOne($c);
			self::logPartner("I seem to have some AccessControl profiles, taking first ID: ".$accessControl->getId());
			self::$partnerSummary['AccessControlID'] = 'OK';
			return $accessControl->getId(); // set this ID on the partner
		}
		self::logPartner("I don't seem to have any AccessControl profiles, going to copy form template partner");
		
		// on fail - create and return ID - from copy partner
		self::logPartner("Performing copy from template partner, will also set default on me");
		$sourcePartner = PartnerPeer::retrieveByPK(myPartnerUtils::PUBLIC_PARTNER_INDEX);
		myPartnerUtils::copyAccessControls($sourcePartner, $partner);
		self::$partnerSummary['AccessControlID'] = 'OK';
		return false; // we don't need to set anything on the partner
	}
	
	public static function createConversionProfile2($partner)
	{
		// try to load default of partner, check if already converted to new
		// on success - return ID of old ? or new ?
		if($partner->getDefaultConversionProfileId())
		{
			self::logPartner("I already have default ConversionProfile ID: ".$partner->getDefaultConversionProfileId());
			self::$partnerSummary['ConversionProfileID'] = 'OK';
			return false; // we don't need to set anything on the partner
		}
		self::logPartner("I don't have default ConversionProfile ID, let's find one");
		
		// try to load default of partner
		$c = new Criteria();
		if(self::$work_from_date)
		{
			$c->addAnd(conversionProfile2Peer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		$c->addAnd(conversionProfile2Peer::PARTNER_ID, $partner->getId());
		$count = conversionProfile2Peer::doCount($c);
		// on success - return ID
		if($count > 0)
		{
			$c->addDescendingOrderByColumn(conversionProfile2Peer::CREATED_AT);
			$conversionProfile = conversionProfile2Peer::doSelectOne($c);
			self::logPartner("I seem to have some ConversionProfiles, taking first ID: ".$conversionProfile->getId());
			self::$partnerSummary['ConversionProfileID'] = 'OK';
			return $conversionProfile->getId(); // set this ID on the partner
		}
		self::logPartner("I don't seem to have any ConversionProfiles, going to copy form template partner");
		
		// on fail - create and return ID - from copy partner
		$sourcePartner = PartnerPeer::retrieveByPK(myPartnerUtils::PUBLIC_PARTNER_INDEX);
		myPartnerUtils::copyConversionProfiles($sourcePartner, $partner);
		self::$partnerSummary['ConversionProfileID'] = 'OK';
		return false; // we don't need to set anything on the partner	
	}
	
	public static function migratePartnerEntries2($partner)
	{
		$selectIndex = 0;
		$selectBulkSize = 100;
		$c = new Criteria();
		$c->addAnd(entryPeer::PARTNER_ID, $partner->getId());
		if(self::$work_from_date)
		{
			$c->addAnd(entryPeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		//$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_DELETED, Criteria::NOT_EQUAL);
		$c->addAscendingOrderByColumn(entryPeer::CREATED_AT);
		$count = entryPeer::doCount($c);
		self::logPartner("going to migrate partner entries. total count: $count");
		
		while(1)
		{
			$output = array();
			$return_val = null;
			exec('php entryMigrationStandAlone.php '.$partner->getId().' '.$selectIndex.' '.$selectBulkSize.' '.self::$work_from_date, $output, $return_val);
			$selectIndex += $selectBulkSize;
			if($return_val)
			{
				break;
			}
		}
		
	}
	
	public static function migratePartnerEntries($partner)
	{
		self::migratePartnerEntries2($partner);
		return;
		$selectIndex = 0;
		$selectBulkSize = 100;
		$c = new Criteria();
		$c->addAnd(entryPeer::PARTNER_ID, $partner->getId());
		if(self::$work_from_date)
		{
			$c->addAnd(entryPeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		//$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_DELETED, Criteria::NOT_EQUAL);
		$c->addAscendingOrderByColumn(entryPeer::CREATED_AT);
		$count = entryPeer::doCount($c);
		self::logPartner("going to migrate partner entries. total count: $count");
		
		$c->setLimit($selectBulkSize);
		
		$failedIds = array();
		while(1)
		{
			$c->setOffset($selectIndex);
			$entries = entryPeer::doSelect($c);
			if(!$entries || count($entries) == 0)
			{
				self::logPartner("     no more entries. index: $selectIndex");
				break;
			}
			
			$res = migrateEntries::migrateEntryList($entries, $partner);
			self::logPartner("     looping from $selectIndex, limit $selectBulkSize");
			if($res == 0 || $res == 2)
			{
				self::logPartner("     ERROR! Entry List migration [start: $selectIndex] [limit: $selectBulkSize] - some entries failed", Zend_Log::ERR);
				$failedIds[$selectIndex] = migrateEntries::getFailedIds();
			}
			else
			{
				self::logPartner("     Entry List migration [start: $selectIndex] [limit: $selectBulkSize] - bulk OK");
			}
			$entries = null;
			$selectIndex = $selectIndex+$selectBulkSize;
		}
		
		$failedEntryIds = array();
		foreach($failedIds as $bulk)
		{
			foreach($bulk as $entryId)
			{
				$failedEntryIds[] = $entryId;
			}
		}
		$failedCount = count($failedEntryIds);
		if(count($failedEntryIds) && count($failedEntryIds) != $count)
		{
			$failed_entries = 'too much failed not showing them here...';
			if(count($failedEntryIds) <= 10)
				$failed_entries = implode(',', $failedEntryIds);
			self::logPartner("Entry migration - PARTIAL FAILURE, some entries failed (failed: [$failedCount] out of $count), IDs: ".$failed_entries, Zend_Log::ERR);
			self::$partnerSummary['entryMigration'] = 'PARTIAL';
		}
		elseif(count($failedEntryIds) && count($failedEntryIds) == $count)
		{
			self::logPartner("Entry migration - FULL FAILURE, All partner entries failed (failed: [$failedCount] out of $count)", Zend_Log::ERR);
			self::$partnerSummary['entryMigration'] = 'FAILURE';
		}
		else
		{
			self::logPartner("Entry migration - Full Success in migrating $count entries");
			self::$partnerSummary['entryMigration'] = 'OK';
		}
		$failedIds = null;
	}
	
	public static function migratePartnerOldConversionProfiles($partner)
	{
		self::logPartner('loading my old conversion profiles');
		$c = new Criteria();
		if(self::$work_from_date)
		{
			$c->addAnd(ConversionProfilePeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		$c->addAnd(ConversionProfilePeer::PARTNER_ID, $partner->getId());
		$profiles = ConversionProfilePeer::doSelect($c);
		self::logPartner('Seems like I have '.count($profiles).' old conversion profiles, going to fix them one by one');
		if(!count($profiles))
		{
			self::logPartner('no old conversion profiles to migrate...');
			self::$partnerSummary['migrateConversionProfile'] = 'OK';
			return true;
		}
		
		$res = migrateConversionProfiles::migrateConversionProfileList($profiles);
		if($res == 0 || $res == 2)
		{
			self::logPartner('old conversion profile migration failed ['.$res.'] failed ids: '.print_r(migrateConversionProfiles::getFailedIds(), true));
			self::$partnerSummary['migrateConversionProfile'] = 'FAILED';
		}
		else
		{
			self::logPartner('old conversion profile migration was successful, all old conversion profiles migrated');
			self::$partnerSummary['migrateConversionProfile'] = 'OK';
		}
	}
	
	public static function migratePartnerUiConfs($partner)
	{
		self::logPartner('loading my uiconfs');
		$c = new Criteria();
		if(self::$work_from_date)
		{
			$c->addAnd(uiConfPeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		$c->addAnd(uiConfPeer::PARTNER_ID, $partner->getId());
		$uiconfs = uiConfPeer::doSelect($c);
		self::logPartner('Seems like I have '.count($uiconfs).' uiconfs, going to fix them one by one');
		if(!count($uiconfs))
		{
			self::logPartner('no uiconfs to migrate...');
			self::$partnerSummary['migrateUiconf'] = 'OK';
			return true;
		}		
		$res = migrateUiconfs::migrateUiconfList($uiconfs);
		if($res == 0 || $res == 2)
		{
			self::logPartner('uiConf migration failed ['.$res.'] failed ids: '.print_r(migrateUiconfs::getFailedIds(), true));
			self::$partnerSummary['migrateUiconf'] = 'FAILED';
		}
		else
		{
			self::logPartner('uiConf migration was successful, all uiconfs migrated');
			self::$partnerSummary['migrateUiconf'] = 'OK';
		}
	}
	
	public static function migratePartnerBulkUploads($partner)
	{
		self::logPartner('loading my bulk-upload jobs');
		$c = new Criteria();
		if(self::$work_from_date)
		{
			$c->addAnd(BatchJobPeer::UPDATED_AT, self::$work_from_date, Criteria::GREATER_EQUAL);
		}		
		$c->addAnd(BatchJobPeer::PARTNER_ID, $partner->getId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_BULKUPLOAD);
		$batchJobs = BatchJobPeer::doSelect($c);
		self::logPartner('Seems like I have '.count($batchJobs).' bulk-upload jobs, going to fix them one by one');
		if(!count($batchJobs))
		{
			self::logPartner('no bulk upload to migrate...');
			self::$partnerSummary['migrateBulkUploads'] = 'OK';
			return true;
		}

		$res = migrateBulkUploads::migrateBUJobs($batchJobs);
		if($res == 0 || $res == 2)
		{
			self::logPartner('BulkUpload migration failed ['.$res.'] failed ids: '.print_r(migrateBulkUploads::getFailedIds(), true));
			self::$partnerSummary['migrateBulkUploads'] = 'failed';
		}
		else
		{
			self::$partnerSummary['migrateBulkUploads'] = 'OK';
			self::logPartner('BulkUpload migration was successful, all BulkUploads migrated');
		}
	}
	
	public static function logScript($msg, $priority = Zend_Log::INFO)
	{
		self::$scriptLog->log("[[".memory_get_usage()."]] ".$msg, $priority);
	}
	
	public static function logPartner($msg, $priority = Zend_Log::INFO)
	{
		self::$partnerLog->log("[[".memory_get_usage()."]] ".$msg, $priority);
	}	
}

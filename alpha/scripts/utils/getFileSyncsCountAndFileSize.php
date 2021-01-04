<?php
define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

if (count($argv) < 5)
{
	print("USAGE: <dc> <startTime> <endTime> <timeFrameSize-in-seconds> <partnerId-optional> ");
	exit(0);
}

$dc = $argv[1];
$startTime = $argv[2];
$endTime = $argv[3];
$timeFrameSize = $argv[4];
$partnerId = isset($argv[5]) ? $argv[5] : null;
if (!is_numeric($dc))
{
	KalturaLog::warning('DC must be numeric');
	exit(0);
}

if (!filter_mydate($startTime)) {
	KalturaLog::warning('StartDate must be a date with format yyyy-mm-dd hh:mm:ss');
	exit(0);
}

if (!filter_mydate($endTime)) {
	KalturaLog::warning('EndDate must be a date with format yyyy-mm-dd hh:mm:ss');
	exit(0);
}

if (!is_numeric($timeFrameSize))
{
	KalturaLog::warning('timeFrameSize must be numeric in seconds');
	exit(0);
}

if (!is_null($partnerId) && !is_numeric($partnerId))
{
	KalturaLog::warning('partnerId must be numeric');
	exit(0);
}

main($dc, $startTime, $endTime, $timeFrameSize, $partnerId);

/**
 * @param $dc
 * @param $startTime
 * @param $endTime
 * @param $timeFrameSize
 */
function main ($dc, $startTime, $endTime, $timeFrameSize, $partnerId)
{
	KalturaLog::debug("Retrieving data from file sync");
	myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
	$results = array();
	try
	{
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
		if (!$con)
		{
			KalturaLog::debug('Could not create propel connection');
			exit(0);
		}
		while (strtotime($startTime) <= strtotime($endTime))
		{
			$currEndTime = date("Y-m-d H:i:s", (strtotime(date($startTime)) + $timeFrameSize));
			if (strtotime($currEndTime) > strtotime($endTime))
			{
				$currEndTime = $endTime;
			}

			FileSyncPeer::setUseCriteriaFilter(false);
			$c = new Criteria();
			$c->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
			$c->add(FileSyncPeer::OBJECT_SUB_TYPE, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$c->add(FileSyncPeer::DC, $dc, Criteria::EQUAL);
			$criterion = $c->getNewCriterion(FileSyncPeer::UPDATED_AT, $startTime, Criteria::GREATER_EQUAL);
			$criterion->addAnd($c->getNewCriterion(FileSyncPeer::UPDATED_AT, $currEndTime, Criteria::LESS_EQUAL));
			$c->addAnd($criterion);
			if (!is_null($partnerId))
			{
				$c->add(FileSyncPeer::PARTNER_ID, $partnerId);

			}
			$fileSyncs = FileSyncPeer::doSelect($c, $con);

			if (!$fileSyncs)
			{
				$startTime = date("Y-m-d H:i:s", (strtotime(date($currEndTime)) + 1));
				continue;
			}
			else
			{
				foreach ($fileSyncs as /** @var FileSync $fileSync **/ $fileSync)
				{
					$status = $fileSync->getStatus();
					if (!isset($results[$status]))
					{
						$results[$fileSync->getStatus()] = array('count' => 0, 'size' => 0, 'status' => $status);
					}
					$results[$status]['count'] ++;
					$results[$status]['size'] += $fileSync->getFileSize();
				}
			}
			$startTime = date("Y-m-d H:i:s", (strtotime(date($currEndTime)) + 1));
			kMemoryManager::clearMemory();
		}
	}
	catch (Exception $e)
	{
		KalturaLog::debug("Error in script " . $e->getMessage());
	}
	print_r($results);
}

function filter_mydate($s) {
	if (preg_match('@^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$@', $s, $m) == false) {
		return false;
	}
	if (checkdate($m[2], $m[3], $m[1]) == false || $m[4] >= 24 || $m[5] >= 60 || $m[6] >= 60) {
		return false;
	}
	return $s;
}
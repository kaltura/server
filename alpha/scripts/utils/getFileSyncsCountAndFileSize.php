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
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
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
			if (is_null($partnerId))
			{
				$query = "SELECT count(*) , sum(file_size), status FROM `file_sync` WHERE file_sync.OBJECT_TYPE=4 AND file_sync.OBJECT_SUB_TYPE=1 AND dc = $dc and updated_at >= '$startTime' and updated_at <= '$currEndTime' group by status;";
			}
			else
			{
				$query = "SELECT count(*) , sum(file_size), status FROM `file_sync` WHERE file_sync.OBJECT_TYPE=4 AND file_sync.OBJECT_SUB_TYPE=1 AND partner_id = $partnerId AND dc = $dc and updated_at >= '$startTime' and updated_at <= '$currEndTime' group by status;";
			}
			try
			{
				$stmt = $con->query($query);
				$data = $stmt->fetchAll();
				if (empty($data))
				{
					$startTime = date("Y-m-d H:i:s", (strtotime(date($currEndTime)) + 1));
					continue;
				}
				else
				{
					foreach ($data as $item)
					{
						$status = $item[2];
						if (!isset($results[$status]))
						{
							$results[$status]= array('count' => 0, 'size' => 0, 'status' => $status);
						}
						$results[$status]['count'] += $item[0];
						$results[$status]['size'] += $item[1];

					}
				}
			}
			catch (Exception $e)
			{
				KalturaLog::debug("Error running query " . $e->getMessage());
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
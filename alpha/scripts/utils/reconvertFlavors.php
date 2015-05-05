<?php
// this chdir can be changed according to environment
chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

function readFileLines($filename)
{
	$result = @file($filename);
	if (!$result)
	{
		return array();
	}
	$result = array_map('trim', $result);
	$result = array_filter($result, 'strlen');
	return $result;
}

function getRunningJobsCount($partnerId, $jobType, $jobSubType)
{
	$c = new Criteria();
	// job type + sub type
	$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getDcIds(), Criteria::IN);
	$c->add(BatchJobLockPeer::PARTNER_ID, $partnerId);
	$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
	$c->add(BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType);
	// group by
	$c->addGroupByColumn(BatchJobLockPeer::DC);
	// select
	$c->addSelectColumn(BatchJobLockPeer::COUNT);
	foreach($c->getGroupByColumns() as $column)
		$c->addSelectColumn($column);
	
	$stmt = BatchJobLockPeer::doSelectStmt($c);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
	$result = array();
	foreach (kDataCenterMgr::getDcIds() as $dc)
	{
		$result[$dc] = 0;
	}
	
	foreach ($rows as $row) 
	{
		$dc = $row['DC'];
		$count = $row[BatchJobLockPeer::COUNT];
		$result[$dc] = $count;
	}
	
	return $result;
}

if ($argc < 8)
{
	die("Usage:\n\tphp ".basename(__file__)." <partner id> <job sub type> <flavor ids file> <processed flavors file> <max jobs per dc> <kalcli location> <first host> [<second host>]\n");
}

$partnerId = $argv[1];
$jobSubType = $argv[2];
$flavorIdsFile = $argv[3];
$processedFlavorIdsFile = $argv[4];
$maxJobsPerDc = $argv[5];
$kalcliLocation = $argv[6];
$firstHost = $argv[7];

$apiServers = array();
$apiServers[] = $firstHost;

if ($argc >= 9)
{
	$secondHost = $argv[8];
	$apiServers[] = $secondHost;
}

$flavorIds = readFileLines($flavorIdsFile);
$processedFlavorIds = readFileLines($processedFlavorIdsFile);

$flavorIds = array_diff($flavorIds, $processedFlavorIds);

$processedFlavorIdsFile = fopen($processedFlavorIdsFile, 'a');

for (;;)
{
	$runningJobs = getRunningJobsCount($partnerId, BatchJobType::CONVERT, $jobSubType);
	KalturaLog::log('running jobs '.print_r($runningJobs, true));
	
	foreach ($runningJobs as $dc => $count)
	{
		while ($count < $maxJobsPerDc)
		{
			$flavorId = array_shift($flavorIds);
			if (!$flavorId)
			{
				KalturaLog::log('Done !');
				die;
			}

			$apiServer = $apiServers[$dc];
			$commandLine = $kalcliLocation . "/generateKs.php $partnerId | " . $kalcliLocation . "kalcli.php -u $apiServer flavorasset reconvert id=$flavorId";

			KalturaLog::log($commandLine);
			$output = array();
			exec($commandLine, $output);
			KalturaLog::log(implode("\n", $output));
			
			fwrite($processedFlavorIdsFile, $flavorId."\n");
			fflush($processedFlavorIdsFile);
			
			$count++;
		}
	}
	sleep(60);
}


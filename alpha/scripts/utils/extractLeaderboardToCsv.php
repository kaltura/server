<?php
require_once(__DIR__ . '/../bootstrap.php');


const OUTPUT_FILE = 'leaderboard.csv';

if ($argc < 5)
	die("Usage: php extractLeaderboardToCsv.php outputPath partnerId gameObjectType gameObjectId"."\n");

$outputPath = $argv[1];
$partnerId = $argv[2];
$gameObjectType = $argv[3];
$gameObjectId = $argv[4];

$redisWrapper = GamePlugin::initGameServicesRedisInstance();
if (!$redisWrapper)
{
	KalturaLog::err('Error: Failed to initialize Redis instance');
	return;
}

$leaderboardScores = getLeaderboardScores($redisWrapper, $partnerId, $gameObjectType, $gameObjectId);

$mapKuserPuser = GamePlugin::createMapKuserToPuser($leaderboardScores);

$userScoreReportsMap = getReportsFromRedis($mapKuserPuser, $partnerId, $redisWrapper);

$rulesMap = array();
$userRows = array();
$userScoresMap = array();

$gameObjectKey = $gameObjectType . '_' . $gameObjectId;

foreach ($mapKuserPuser as $kuserId => $puser)
{
	if (!isset($userScoreReportsMap[$puser]))
	{
		break;
	}
	
	$userScoreReport = $userScoreReportsMap[$puser];
	
	$userScoreReport = str_replace("'", "\"", $userScoreReport);
	$decodedJson = json_decode($userScoreReport);
	
	if (!$decodedJson->gameObjectsReports)
	{
		KalturaLog::info("Missing gameObjectReports for user $puser");
		continue;
	}
	
	foreach ($decodedJson->gameObjectsReports as $gameObject)
	{
		if ($gameObject->id != $gameObjectKey)
		{
			continue;
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puser);
		if (!$kuser)
		{
			KalturaLog::info("Could not find user $puser");
			break;
		}
		
		$userTotalScore = floor($leaderboardScores[$kuserId]);
		
		$userRow = array($userTotalScore, $puser, $kuser->getScreenName(), $kuser->getEmail());
		if (!$gameObject->rulesData)
		{
			KalturaLog::info("Missing rulesData for user $puser");
			break;
		}
		
		$scores = array();
		
		foreach ($gameObject->rulesData as $rule)
		{
			if (!isset($rule->id))
			{
				KalturaLog::info("Missing ruleId for user $puser");
				break;
			}
			
			if (!isset($rulesMap[$rule->id]))
			{
				$rulesMap[$rule->id] = $rule->description;
			}
			
			$scores[$rule->id] = $rule->score;
		}
		
		if (!compareSumOfScoresWithTotalScore($scores, $userTotalScore))
		{
			KalturaLog::info("Error: User $puser total score of $userTotalScore, does not match with sum of rules from report");
			KalturaLog::info('Rules ids and scores: ' . print_r($scores));
			return;
		}
		
		$userScoresMap[$puser] = $scores;
		
		$userRows[$puser] = $userRow;
		
		break;
	}
	
	if (!isset($userScoresMap[$puser]))
	{
		KalturaLog::info("Could not find gameObject $gameObjectId of Type $gameObjectType for User $puser");
	}
}

$userRowsWithScores = array();

foreach (array_keys($leaderboardScores) as $kuserId)
{
	$puser = $mapKuserPuser[$kuserId];
	
	if (!isset($userScoresMap[$puser]))
	{
		continue;
	}
	
	foreach (array_keys($rulesMap) as $ruleId)
	{
		if (!isset($userScoresMap[$puser][$ruleId]))
		{
			$userScoresMap[$puser][$ruleId] = 0;
		}
	}
	
	ksort($userScoresMap[$puser]);
	
	$userRowsWithScores[] = array_merge($userRows[$puser], $userScoresMap[$puser]);
}

$userRowsWithRanks = addRanks($userRowsWithScores);

$dataToWrite = array();
$headerRow = array('rank', 'score', 'userId', 'userName', 'userEmail');
$dataToWrite[] = array_merge($headerRow, array_values($rulesMap));
$dataToWrite = array_merge($dataToWrite, $userRowsWithRanks);

writeLeaderboardToCsv($dataToWrite, $outputPath);


function getLeaderboardScores($redisWrapper, $partnerId, $gameObjectType, $gameObjectId)
{
	$leaderboardRedisKey = $partnerId . '_' . $gameObjectType . '_' . $gameObjectId;
	
	$leaderboardScores = $redisWrapper->doZrevrange($leaderboardRedisKey, 0, -1);
	if (!$leaderboardScores)
	{
		KalturaLog::err("No results found for key $leaderboardRedisKey");
		return array();
	}
	
	return $leaderboardScores;
}

function getReportsFromRedis($mapKuserPuser, $partnerId, $redisWrapper)
{
	$userScoreReportsMap = array();
	
	foreach ($mapKuserPuser as $kuserId => $puser)
	{
		$userRedisKey = $partnerId . '_report_' . $kuserId;
		
		$userScoreReport = $redisWrapper->doGet($userRedisKey);
		
		if ($userScoreReport)
		{
			$userScoreReportsMap[$puser] = $userScoreReport;
		}
	}
	
	return $userScoreReportsMap;
}

function compareSumOfScoresWithTotalScore($scores, $totalScore)
{
	$sumScores = 0;
	foreach (array_values($scores) as $score)
	{
		$sumScores += $score;
	}
	
	if ($sumScores != $totalScore)
	{
		return false;
	}
	
	return true;
}

function addRanks($userRows)
{
	$i = 0;
	$userRowsWithRanks = array();
	foreach ($userRows as $userRow)
	{
		$rank = $i + 1;
		$userRowsWithRanks[] = array_merge(array($rank), $userRow);
		$i++;
	}
	
	return $userRowsWithRanks;
}

function writeLeaderboardToCsv($dataToWrite, $outputPath)
{
	$file = fopen($outputPath . '/' . OUTPUT_FILE, 'w');
	if (!$file)
	{
		KalturaLog::err("Error: Failed to create file $outputPath");
		return;
	}
	
	foreach ($dataToWrite as $row)
	{
		fputcsv($file, $row);
	}
	fclose($file);
}

<?php
require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 5)
{
	die("Usage: php createGameObjectCsvReport.php outputPath partnerId gameObjectType gameObjectId"."\n");
}

$outputPath = $argv[1];
$partnerId = $argv[2];
$gameObjectType = $argv[3];
$gameObjectId = $argv[4];

$outputPath =  $outputPath . '/leaderboardCsvReport_' . $partnerId . '_' . time() . '.csv';

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

getUserScoreReports($rulesMap, $userRows,$userScoresMap);
ksort($rulesMap);

$userRowsWithScores = addScoresToUserRows($userScoresMap);
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
	
	foreach ($mapKuserPuser as $kuserId => $puserId)
	{
		$userReportRedisKey = $partnerId . '_report_' . $kuserId;
		
		$userScoreReport = $redisWrapper->doGet($userReportRedisKey);
		
		if ($userScoreReport)
		{
			$userScoreReportsMap[$puserId] = $userScoreReport;
		}
	}
	
	return $userScoreReportsMap;
}

function getUserScoreReports(&$rulesMap, &$userRows, &$userScoresMap)
{
	global $partnerId;
	global $gameObjectType;
	global $gameObjectId;
	global $leaderboardScores;
	global $mapKuserPuser;
	global $userScoreReportsMap;
	
	$gameObjectKey = $gameObjectType . '_' . $gameObjectId;
	
	foreach ($mapKuserPuser as $kuserId => $puserId)
	{
		if (!isset($userScoreReportsMap[$puserId]))
		{
			break;
		}
		
		$userScoreReport = $userScoreReportsMap[$puserId];
		$decodedJson = json_decode($userScoreReport);
		
		if (!$decodedJson)
		{
			KalturaLog::info("Failed to decode report json for user $puserId:\n" . print_r($userScoreReport));
			continue;
		}
		
		if (!isset($decodedJson->gameObjectsReports))
		{
			KalturaLog::info("Missing gameObjectReports for user $puserId");
			continue;
		}
		
		foreach ($decodedJson->gameObjectsReports as $gameObject)
		{
			if ($gameObject->id != $gameObjectKey)
			{
				continue;
			}
			
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			if (!$kuser)
			{
				KalturaLog::info("Could not find user $puserId");
				break;
			}
			
			$userTotalScore = floor($leaderboardScores[$kuserId]);
			
			$userRow = array($userTotalScore, $puserId, $kuser->getFullName(), $kuser->getEmail());
			if (!isset($gameObject->rulesData))
			{
				KalturaLog::info("Missing rulesData for user $puserId");
				break;
			}
			
			$scores = array();
			
			foreach ($gameObject->rulesData as $rule)
			{
				if (!isset($rule->id))
				{
					KalturaLog::info("Missing ruleId for user $puserId");
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
				KalturaLog::info("Error: User $puserId total score of $userTotalScore, does not match with sum of rules from report");
				KalturaLog::info('Rules ids and scores: ' . print_r($scores));
				return;
			}
			
			$userScoresMap[$puserId] = $scores;
			
			$userRows[$puserId] = $userRow;
			
			break;
		}
		
		if (!isset($userScoresMap[$puserId]))
		{
			KalturaLog::info("Could not find gameObject $gameObjectId of Type $gameObjectType for User $puserId");
		}
	}
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

function addScoresToUserRows(&$userScoresMap)
{
	global $leaderboardScores;
	global $mapKuserPuser;
	global $rulesMap;
	global $userRows;
	
	$userRowsWithScores = array();
	
	foreach (array_keys($leaderboardScores) as $kuserId)
	{
		$puserId = $mapKuserPuser[$kuserId];
		
		if (!isset($userScoresMap[$puserId]))
		{
			continue;
		}
		
		foreach (array_keys($rulesMap) as $ruleId)
		{
			if (!isset($userScoresMap[$puserId][$ruleId]))
			{
				$userScoresMap[$puserId][$ruleId] = 0;
			}
		}
		
		ksort($userScoresMap[$puserId]);
		
		$userRowsWithScores[] = array_merge($userRows[$puserId], $userScoresMap[$puserId]);
	}
	
	return $userRowsWithScores;
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
	$file = fopen($outputPath, 'w');
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

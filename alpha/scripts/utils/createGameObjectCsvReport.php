<?php
require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 5)
{
	die("Usage: php createGameObjectCsvReport.php outputPath partnerId gameObjectType gameObjectId [rulesPath]"."\n");
}

$outputPath = $argv[1];
$partnerId = $argv[2];
$gameObjectType = $argv[3];
$gameObjectId = $argv[4];

if (isset($argv[5]))
{
	$rulesPath = $argv[5];
}

$time = date('Y-m-d_[H-i-s]', time());
$outputPath =  $outputPath . '/leaderboardCsvReport_' . $partnerId . '_' . $time . '.csv';

$redisWrapper = GamePlugin::initGameServicesRedisInstance();
if (!$redisWrapper)
{
	KalturaLog::err('Error: Failed to initialize Redis instance');
	exit(1);
}

$leaderboardScores = getLeaderboardScores($redisWrapper, $partnerId, $gameObjectType, $gameObjectId);
$mapKuserPuser = GamePlugin::createMapKuserToPuser($leaderboardScores);
$userScoreReportsMap = getAllUsersReportsFromRedis($mapKuserPuser, $partnerId, $redisWrapper);

$rulesMap = array();
$userRows = array();
$userScoresMap = array();

readRulesAndScoresFromUsersReports($rulesMap, $userRows,$userScoresMap);

if (isset($rulesPath))
{
	$rulesMap = readRulesMapFromFile($rulesPath);
}
else
{
	ksort($rulesMap);
}

$userRowsWithScores = addScoresToUserRows($userScoresMap);
$userRowsWithRanks = addRanksToUserRows($userRowsWithScores);

writeLeaderboardToCsv($outputPath, array_values($rulesMap), $userRowsWithRanks);

exit(0);


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

function getAllUsersReportsFromRedis($mapKuserPuser, $partnerId, $redisWrapper)
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

function readRulesAndScoresFromUsersReports(&$rulesMap, &$userRows, &$userScoresMap)
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
				exit(0);
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

function readRulesMapFromFile($filePath)
{
	$rulesMapFromFile = array();
	
	try
	{
		$content = file_get_contents($filePath);
		if (!$content)
		{
			KalturaLog::info('Failed to load rules file');
			exit(1);
		}
		
		$decodedJson = json_decode($content);
		if (!$decodedJson)
		{
			KalturaLog::info('Failed to decode rules json');
			exit(1);
		}
		
		if (!isset($decodedJson->rules))
		{
			KalturaLog::info('Missing rules in rules file');
			exit(1);
		}
		
		foreach ($decodedJson->rules as $rule)
		{
			if (!isset($rule->id) || !isset($rule->description) || !isset($rule->order))
			{
				KalturaLog::info('Missing id, description, or order inside a rule');
				exit(1);
			}
			
			$rulesMapFromFile[$rule->order] = array('id' => $rule->id, 'description' => $rule->description);
		}
	}
	catch (Exception $e)
	{
		KalturaLog::info('Failed to load rules file: ' . $e);
		exit(1);
	}
	
	$rulesMap = array();
	if ($rulesMapFromFile)
	{
		ksort($rulesMapFromFile);
		foreach ($rulesMapFromFile as $ruleOrder => $ruleDetails)
		{
			$rulesMap[$ruleDetails['id']] = $ruleDetails['description'];
		}
	}
	
	return $rulesMap;
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

function addScoresToUserRows($userScoresMap)
{
	global $leaderboardScores;
	global $mapKuserPuser;
	global $rulesMap;
	global $userRows;
	
	$userRowsWithScores = array();
	$orderedUserScoresMap = array();
	
	foreach (array_keys($leaderboardScores) as $kuserId)
	{
		$puserId = $mapKuserPuser[$kuserId];
		
		if (!isset($userScoresMap[$puserId]))
		{
			continue;
		}
		
		$orderedUserScoresMap[$puserId] = array();
		
		foreach (array_keys($rulesMap) as $ruleId)
		{
			if (isset($userScoresMap[$puserId][$ruleId]))
			{
				$orderedUserScoresMap[$puserId][$ruleId] = $userScoresMap[$puserId][$ruleId];
			}
			else
			{
				$orderedUserScoresMap[$puserId][$ruleId] = 0;
			}
		}
		
		$userRowsWithScores[] = array_merge($userRows[$puserId], $orderedUserScoresMap[$puserId]);
	}
	
	return $userRowsWithScores;
}

function addRanksToUserRows($userRows)
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

function writeLeaderboardToCsv($outputPath, $rulesNames, $userRowsWithRanks)
{
	$dataToWrite = array();
	$headerRow = array('rank', 'score', 'userId', 'userName', 'userEmail');
	$dataToWrite[] = array_merge($headerRow, $rulesNames);
	$dataToWrite = array_merge($dataToWrite, $userRowsWithRanks);
	
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

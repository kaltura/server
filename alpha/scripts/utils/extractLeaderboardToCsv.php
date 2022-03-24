<?php
require_once(__DIR__ . '/../bootstrap.php');


function getMapKuserToPuser($redisWrapper, $partnerId, $gameObjectType, $gameObjectId)
{
	$redisKey = $partnerId . '_' . $gameObjectType . '_' . $gameObjectId;
	
	$results = $redisWrapper->doZrevrange($redisKey, 0, -1);
	if (!$results)
	{
		print("No results found for key $redisKey");
		return array();
	}
	
	$mapKuserPuser = GamePlugin::createMapKuserToPuser($results);
	
	return $mapKuserPuser;
}

function addRanks($userRows)
{
	usort($userRows, function($a, $b)
	{
		return $b[0] - $a[0];
	});
	
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

function writeLeaderboardToCsv($dataToWrite)
{
	$filename = 'leaderboard.csv';
	$file = fopen($filename, 'w');
	if (!$file)
	{
		print('Failed to create file');
		return;
	}
	
	foreach ($dataToWrite as $row)
	{
		fputcsv($file, $row);
	}
	fclose($file);
}


//
// Script Begins Here //
//

if ($argc < 3)
    die("Usage: php extractLeaderboardToCsv.php partnerId gameObjectType gameObjectId"."\n");

$partnerId = $argv[1];
$gameObjectType = $argv[2];
$gameObjectId = $argv[3];

$redisWrapper = GamePlugin::initGameServicesRedisInstance();
if (!$redisWrapper)
{
	throw new KalturaAPIException(KalturaErrors::FAILED_INIT_REDIS_INSTANCE);
}

$mapKuserPuser = getMapKuserToPuser($redisWrapper, $partnerId, $gameObjectType, $gameObjectId);


$rulesMap = array();
$userRows = array();
$userScoresMap = array();

foreach ($mapKuserPuser as $kuser => $puser)
{
	$userRedisKey = $partnerId . '_report_' . $kuser;
	
	$redisValue = $redisWrapper->doGet($userRedisKey);
	
	if ($redisValue)
	{
		$redisValue = str_replace("'", "\"", $redisValue);
		$decodedJson = json_decode($redisValue);
		
		$gameObjectKey = $gameObjectType . '_' . $gameObjectId;
		
		$scores = array();
		
		foreach ($decodedJson->gameObjectsReports as $gameObject)
		{
			if ($gameObject->id == $gameObjectKey)
			{
				print_r($gameObject);
				
				$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puser);
				$userRow = array($gameObject->totalScore, $puser, $kuser->getScreenName(), $kuser->getEmail());
				
				foreach ($gameObject->rulesData as $rule)
				{
					if (!isset($rulesMap[$rule->id]))
					{
						$rulesMap[$rule->id] = $rule->description;
					}
					
					$scores[$rule->id] = $rule->score;
				}
				
				$userScoresMap[$puser] = $scores;
				
				$userRows[$puser] = $userRow;
				
				break;
			}
		}
		
		if (!$scores)
		{
			print("Could not find gameObject $gameObjectId of Type $gameObjectType for User $puser");
		}
	}
}

$userRowsWithScores = array();

foreach (array_values($mapKuserPuser) as $puser)
{
	if (!isset($userScoresMap[$puser]))
	{
		break;
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
$dataToWrite[] = array_merge(array('rank', 'score', 'userId', 'userName', 'userEmail'), array_values($rulesMap));
$dataToWrite = array_merge($dataToWrite, $userRowsWithRanks);

writeLeaderboardToCsv($dataToWrite);
<?php

function createSphinxConnection($sphinxServer, $port = 9312)
{
	$dsn = "mysql:host=$sphinxServer;port=$port;";
	
	try
	{
		$con = new PDO($dsn);
		return $con;
	}
	catch(PropelException $pex)
	{
		KalturaLog::alert($pex->getMessage());
		throw new PropelException("Database error");
	}
}

function issueQuery($pdo, $sql)
{
	$stmt = $pdo->query($sql);
	if(!$stmt)
	{
		list($sqlState, $errCode, $errDescription) = $pdo->errorInfo();
		return "Invalid sphinx query [$sql]\nSQLSTATE error code [$sqlState]\nDriver error code [$errCode]\nDriver error message [$errDescription]";
	}
	
	$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
	return $ids;
}

function addQuotes($str)
{
	return "'{$str}'";
}

if ($argc < 3)
	die("Usage:\n\tphp sphinxCompatCheck <sphinx1 host> <sphinx1 port> <sphinx2 host> <sphinx2 port>\n");
	
$conn1 = createSphinxConnection($argv[1], $argv[2]);
$conn2 = createSphinxConnection($argv[3], $argv[4]);
$strictMode = false;

$serverTime1 = 0;
$serverTime2 = 0;

$fp = fopen("php://stdin","r");
while($line = stream_get_line($fp, 65535, "\n"))
{
	$selectPos = strpos($line, 'SELECT ');
	if ($selectPos === false)
		continue;
	$query = substr($line, $selectPos);

	$startTime = microtime(true);
	$res1 = issueQuery($conn1, $query);
	$serverTime1 += microtime(true) - $startTime;

	$startTime = microtime(true);
	$res2 = issueQuery($conn2, $query);
	$serverTime2 += microtime(true) - $startTime;
	
	if (strpos($query, 'ORDER BY ') === false && is_array($res1) && is_array($res2))
	{
		sort($res1);
		sort($res2);
	}

	if ($res1 != $res2)
	{
		$limit = null;
		if (preg_match('/LIMIT (\d+)/', $query, $matches))
			$limit = $matches[1];

		if (!$strictMode)
		{
			if ($limit && count($res2) == $limit)
				continue;		// the new config returned max results

			$removedIds = array_diff($res1, $res2);
			if (!$removedIds)
				continue;		// no ids were removed
		}

		$sev = 'ERROR';
		if (strpos($query, 'ORDER BY ') === false && is_array($res1) && is_array($res2) && count($res1) == 1000 && count($res2) == 1000)
			$sev = 'WARNING';
		print "\n$sev - $query\n";
		print 'Old count: '.count($res1)."\n";
		print 'New count: '.count($res2)."\n";
		$removedIds = array_diff($res1, $res2);
		if ($removedIds)
			print 'Removed ids: '.implode(',', array_map('addQuotes', $removedIds))."\n";
		$addedIds = array_diff($res2, $res1);
		if ($addedIds)
			print 'Added ids: '.implode(',', array_map('addQuotes', $addedIds))."\n";
	}
	else
		print '.';
}
fclose($fp);

print "\nDone\nServer1 took $serverTime1\nServer2 took $serverTime2\n";

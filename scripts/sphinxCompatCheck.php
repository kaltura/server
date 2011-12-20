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
	
	$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 2);
	return $ids;
}

if ($argc < 3)
	die("Usage:\n\tphp sphinxCompatCheck <sphinx1> <sphinx2>\n");


$conn1 = createSphinxConnection($argv[1]);
$conn2 = createSphinxConnection($argv[2]);

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
	
	if ($res1 != $res2)
		print "ERROR - $query\n";
	else
		print '.';
	sleep(.1);
}
fclose($fp);

print "\nDone\nServer1 took $serverTime1\nServer2 took $serverTime2\n";

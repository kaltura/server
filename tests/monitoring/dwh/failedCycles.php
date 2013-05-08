<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'statuses:',
	'time-column:',
	'hours:',
));


if(!isset($options['statuses']))
{
	echo "Argument statuses is required";
	exit(-1);
}

if(!isset($options['time-column']))
{
	echo "Argument time-column is required";
	exit(-1);
}

if(!isset($options['hours']))
{
	echo "Argument hours is required";
	exit(-1);
}

$timeColumn = $options['time-column'];
$hours = $options['hours'];

$statuses = explode(',', $options['statuses']);
foreach($statuses as &$status)
	$status = "'" . trim($status) . "'";
$statuses = implode(', ', $statuses);

// start
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();

$config = parse_ini_file(__DIR__ . '/../config.ini', true);
try
{
	// connect to the db
	$dwhPdo = new PDO($config['dwh']['dsn'], $config['dwh']['username'], $config['dwh']['password']);
	
	// insert or update sphinx log
	$query = "
	SELECT f.file_name, c.status
	FROM (
			SELECT *
			FROM kalturadw_ds.cycles c
			WHERE (status IN ($statuses) AND c.$timeColumn < NOW() - INTERVAL $hours HOUR)) c,
		kalturadw_ds.files f
	WHERE c.cycle_id = f.cycle_id";
	
	$selectStatement = $dwhPdo->query($query);
	if($selectStatement === false)
		throw new Exception("Query failed: $query");
	$failedFiles = $selectStatement->fetchAll(PDO::FETCH_NAMED);
		
	$end = microtime(true);
	
	foreach($failedFiles as $failedFile)
	{
		$error = new KalturaMonitorError();
		$error->description = $failedFile['file_name'] . " cycle failed with status " . $failedFile['status'];
		$error->level = KalturaMonitorError::CRIT;
		$monitorResult->errors[] = $error;
	}
	
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = count($failedFiles);
	if($monitorResult->value)
		$monitorResult->description = "$monitorResult->value cycles failed";
	else
		$monitorResult->description = "All cycles succeeded";
	
	echo "$monitorResult";
	exit(0);
}
catch(PDOException $pdoe)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $pdoe->getCode();
	$error->description = $pdoe->getMessage();
	$error->level = KalturaMonitorError::CRIT;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = get_class($pdoe) . ": " . $pdoe->getMessage();
	
	echo "$monitorResult";
	exit(0);
}
catch(Exception $e)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $e->getCode();
	$error->description = $e->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = $e->getMessage();
	
	echo "$monitorResult";
	exit(0);
}

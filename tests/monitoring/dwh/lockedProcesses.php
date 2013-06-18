<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'daily-lock:',
	'hourly-lock:',
	'etl-processes-lock:',
));


if(!isset($options['daily-lock']))
{
	echo "Argument daily-lock is required";
	exit(-1);
}

if(!isset($options['hourly-lock']))
{
	echo "Argument hourly-lock is required";
	exit(-1);
}

if(!isset($options['etl-processes-lock']))
{
	echo "Argument etl-processes-lock is required";
	exit(-1);
}

$dailyLock = $options['daily-lock'];
$hourlyLock = $options['hourly-lock'];
$etlProcLock = $options['etl-processes-lock'];


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
	SELECT lock_name FROM kalturadw_ds.LOCKS
	WHERE TIME_TO_SEC(TIMEDIFF(NOW(), lock_time)) > IF (lock_name = 'daily_lock', $dailyLock, IF(lock_name LIKE 'hourly_%', $hourlyLock, $etlProcLock))
	AND lock_state = 1";
	
	$selectStatement = $dwhPdo->query($query);
	if($selectStatement === false)
		throw new Exception("Query failed: $query");
	$locks = $selectStatement->fetchAll(PDO::FETCH_COLUMN, 0);
		
	$end = microtime(true);
	
	foreach($locks as $lock)
	{
		$error = new KalturaMonitorError();
		$error->description = "$lock is locked";
		$error->level = KalturaMonitorError::CRIT;
		$monitorResult->errors[] = $error;
	}
	
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = count($locks);
	if($monitorResult->value)
		$monitorResult->description = "$monitorResult->value processes are locked";
	else
		$monitorResult->description = "All processes released";
	
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

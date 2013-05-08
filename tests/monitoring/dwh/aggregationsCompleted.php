<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'hours:',
));


if(!isset($options['hours']))
{
	echo "Argument hours is required";
	exit(-1);
}

$hours = $options['hours'];


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
	SELECT  aggr_name,
	        DATE(date_id) DATE,
	        MAX(data_insert_time) latest_data_insert_time
	FROM kalturadw.aggr_managment
	WHERE   (IFNULL(start_time,DATE(19700101)) < data_insert_time
	                        OR
	                        start_time > end_time /* Handle Failed aggregations*/)
	        AND data_insert_time < NOW() - INTERVAL $hours HOUR
	GROUP BY date_id, aggr_name
	ORDER BY date_id, aggr_name";
	
	$selectStatement = $dwhPdo->query($query);
	if($selectStatement === false)
		throw new Exception("Query failed: $query");
	$aggregations = $selectStatement->fetchAll(PDO::FETCH_COLUMN, 0);
		
	$end = microtime(true);
	
	foreach($aggregations as $aggregation)
	{
		$error = new KalturaMonitorError();
		$error->description = "$aggregation aggregation did not complete";
		$error->level = KalturaMonitorError::CRIT;
		$monitorResult->errors[] = $error;
	}
	
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = count($aggregations);
	if($monitorResult->value)
		$monitorResult->description = "$monitorResult->value aggregations did not complete";
	else
		$monitorResult->description = "All aggregations completed";
	
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

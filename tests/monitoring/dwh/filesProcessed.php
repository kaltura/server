<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$fileTypes = array(
	'events'		=> 1,
	'fms'			=> 2,
	'akamai'		=> 3,
	'akamai-bw'		=> 4,
	'limelight-bw'	=> 5,
	'level3-bw'		=> 6,
	'akamai-rtmp'	=> 7,
);


$options = getopt('', array(
	'debug',
	'file-type:',
	'hours:',
));

if(!isset($options['file-type']))
{
	echo "Argument file-type is required";
	exit(-1);
}

if(!isset($fileTypes[$options['file-type']]))
{
	echo "Argument file-type is invalid";
	exit(-1);
}

if(!isset($options['hours']))
{
	echo "Argument hours is required";
	exit(-1);
}

$fileType = $options['file-type'];
$fileTypeValue = $fileTypes[$fileType];
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
	SELECT	COUNT(*)
	FROM	kalturadw_ds.files f,
			kalturadw_ds.cycles c
	WHERE	c.process_id = $fileTypeValue
	AND 	f.cycle_id = c.cycle_id
	AND		c.STATUS='DONE'
	AND		f.insert_time > NOW() - INTERVAL $hours HOUR";
	
	$selectStatement = $dwhPdo->query($query);
	if($selectStatement === false)
		throw new Exception("Query failed: $query");
	$count = $selectStatement->fetchColumn(0);
		
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = $count;
	$monitorResult->description = "$count $fileType files processed";
	
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

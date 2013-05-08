<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'hostname:',
));

if(!isset($options['hostname']))
{
	echo "Argument hostname is required";
	exit(-1);
}
$hostname = $options['hostname'];


// start
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();

$config = parse_ini_file(__DIR__ . '/../config.ini', true);
try
{
	// connect to the db
	$pdo = new PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
	
	// diff between now and last status
	$lastStatusQuery = "SELECT TIMESTAMPDIFF(SECOND, last_status, NOW()) FROM scheduler WHERE host = '$hostname'";
	$lastStatusStatement = $pdo->query($lastStatusQuery);
	if($lastStatusStatement === false)
		throw new Exception("Query failed: $lastStatusQuery");
	$lastStatus = $lastStatusStatement->fetchColumn(0);
	
	if($lastStatus === false) // no records found for that scheduler hostname
		throw new Exception("Schedule not found in host $hostname");
	if(is_null($lastStatus)) // no status ever sent
		throw new Exception("Schedule status never sent for host $hostname");
	
	// done
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;;
	$monitorResult->value = $lastStatus;
	$monitorResult->description = "Last status sent $lastStatus seconds ago";
	
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

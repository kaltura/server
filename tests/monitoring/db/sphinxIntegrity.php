<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'hostname:',
	'time-offset:',
	'table:',
	'index:',
	'object-type:',
	'check-create-time',
));

if(!isset($options['hostname']))
{
	echo "Argument hostname is required";
	exit(-1);
}

if(!isset($options['time-offset']))
{
	echo "Argument time-offset is required";
	exit(-1);
}

if(!isset($options['table']))
{
	echo "Argument table is required";
	exit(-1);
}

if(!isset($options['index']))
{
	echo "Argument index is required";
	exit(-1);
}

if(!isset($options['object-type']))
{
	echo "Argument object-type is required";
	exit(-1);
}

$hostname = $options['hostname'];
$timeOffset = $options['time-offset'];
$table = $options['table'];
$index = $options['index'];
$objectType = $options['object-type'];
$dateColumn = 'updated_at';
if(isset($options['check-create-time']))
	$dateColumn = 'created_at';

// define time period - an hour ago
$timestamp = time() - $timeOffset;
$timestampText = date('Y-m-d H:i:s', $timestamp);


// start
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();

$config = parse_ini_file(__DIR__ . '/../config.ini', true);
try
{
	// connect to the db
	$pdo = new PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
	$logPdo = new PDO($config['sphinx-log']['dsn'], $config['sphinx-log']['username'], $config['sphinx-log']['password']);
	$sphinxPdo = new PDO("mysql:host=$hostname;port=9312;");
	
	// define queries
	$tableQuery = "SELECT COUNT(id) FROM $table WHERE $dateColumn > '$timestampText'";
	$logQuery = "SELECT COUNT(DISTINCT object_id) FROM sphinx_log WHERE object_type = '$objectType' AND created_at > '$timestampText'";
	$sphinxQuery = "SELECT id, ($dateColumn > $timestamp) AS cnd FROM $index WHERE cnd > 0";

	$tableStatement = $pdo->query($tableQuery);
	if($tableStatement === false)
		throw new Exception("Query failed: $tableQuery");
		
	$logStatement = $logPdo->query($logQuery);
	if($logStatement === false)
		throw new Exception("Query failed: $logQuery");
		
	$sphinxStatement = $sphinxPdo->query($sphinxQuery);
	if($sphinxStatement === false)
		throw new Exception("Query failed: $sphinxQuery");
	
	$tableCount = intval($tableStatement->fetchColumn(0));
	$logCount = intval($logStatement->fetchColumn(0));
	$sphinxCount = null;
	
	$metaStatement = $sphinxPdo->query("show meta", PDO::FETCH_NAMED);
	$meta = $metaStatement->fetchAll(PDO::FETCH_NAMED);
	foreach($meta as $variable)
	{
		if($variable['Variable_name'] == 'total_found')
		{
			$sphinxCount = intval($variable['Value']);
			break;
		}
	}
	
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	if(is_null($sphinxCount))
		throw new Exception("Sphinx count not found");
		
	if($tableCount == $logCount && $tableCount == $sphinxCount)
	{
		$monitorResult->value = 1;
		if($tableCount)
			$monitorResult->description = "$tableCount $objectType objects changed, updated in the log and in the sphinx";
		else
			$monitorResult->description = "No updates done";
	}
	else
	{
		if($tableCount != $logCount)
		{
			$error = new KalturaMonitorError();
			$error->description = "Sphinx log contains $logCount records although $tableCount $objectType objects changed";
			$error->level = KalturaMonitorError::CRIT;
			$monitorResult->errors[] = $error;
		}
		
		if($tableCount != $sphinxCount)
		{
			$error = new KalturaMonitorError();
			$error->description = "Sphinx updated with $sphinxCount records although $tableCount $objectType objects changed";
			$error->level = KalturaMonitorError::CRIT;
			$monitorResult->errors[] = $error;
		}
		
		$monitorResult->value = 0;
		$monitorResult->description = "Sphinx integrity is broken";
	}
	
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

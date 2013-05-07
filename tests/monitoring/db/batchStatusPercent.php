<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaEnums.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'debug',
	'total-count:',
	'status:',
	'job-type:',
	'job-sub-type:',
));



if(!isset($options['total-count']))
{
	echo "Argument total-count is required";
	exit(-1);
}
$totalCount = $options['total-count'];
if(!is_numeric($totalCount) || $totalCount <= 0)
{
	echo "Argument total-count [$totalCount] is invalid, only positive numeric values are acceptable";
	exit(-1);
}




if(!isset($options['status']))
{
	echo "Argument status is required";
	exit(-1);
}
$statusConst = $options['status'];
if(!defined("KalturaBatchJobStatus::$statusConst"))
{
	echo "Argument status [$statusConst] is invalid, only KalturaBatchJobStatus constants are acceptable";
	exit(-1);
}
$status = constant("KalturaBatchJobStatus::$statusConst");





if(!isset($options['job-type']))
{
	echo "Argument status is required";
	exit(-1);
}
$jobTypeConst = $options['job-type'];
if(!defined("KalturaBatchJobType::$jobTypeConst"))
{
	echo "Argument job-type [$jobTypeConst] is invalid, only KalturaBatchJobStatus constants are acceptable";
	exit(-1);
}
$jobType = constant("KalturaBatchJobType::$jobTypeConst");





$jobSubType = null;
if(isset($options['job-sub-type']))
{
	$jobSubType = $options['job-sub-type'];
	if(!is_numeric($jobSubType))
	{
		if(!defined($jobSubType))
		{
			echo "Argument job-sub-type [$jobSubType] is invalid";
			exit(-1);
		}
		
		$jobSubType = constant($jobSubType);
		if(!is_numeric($jobSubType))
		{
			echo "Argument job-sub-type [$jobSubType] is invalid, dynamic enums are not supported";
			exit(-1);
		}
	}
}



// start
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();

$config = parse_ini_file(__DIR__ . '/../config.ini', true);
try
{
	// connect to the db
	$pdo = new PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);

	
	// find the dynamic enum core value
	if(!is_numeric($jobType))
	{
		$enumName = 'BatchJobType';
		list($pluginName, $valueName) = explode('.', $jobType, 2);
		$findEnumValueQuery = "SELECT id FROM dynamic_enum WHERE enum_name = '$enumName' AND plugin_name = '$pluginName' AND value_name = '$valueName'";
		$enumValuesStatement = $pdo->query($findEnumValueQuery);
		if($enumValuesStatement === false)
			throw new Exception("Query failed: $findEnumValueQuery");
		$jobType = $enumValuesStatement->fetchColumn(0);
	}
	
	
	// prepare jobs query
	$conditions = array("job_type = $jobType");
	if(!is_null($jobSubType))
		$conditions[] = "job_sub_type = $jobSubType";
	$conditions = implode(' AND ', $conditions);
	
	
	// find last job id
	$findLastQuery = "SELECT id FROM batch_job_sep WHERE $conditions ORDER BY id DESC LIMIT 1";
	$findLastStatement = $pdo->query($findLastQuery);
	if($findLastStatement === false)
		throw new Exception("Query failed: $findLastQuery");
	$last = $findLastStatement->fetchColumn(0);
	
	if($last === false) // no records found for that job id
	{
		$end = microtime(true);
		$monitorResult->executionTime = $end - $start;;
		$monitorResult->value = 0;
		if(is_null($jobSubType))
			$monitorResult->description = "No records found for job type $jobTypeConst";
		else
			$monitorResult->description = "No records found for job type $jobTypeConst, sub type $jobSubType";
		
		echo "$monitorResult";
		exit(0);
	}
	
	
	// find first job id according to $totalCount offset
	$findFirstQuery = "SELECT id FROM batch_job_sep WHERE $conditions ORDER BY id DESC LIMIT $totalCount, 1";
	$findFirstStatement = $pdo->query($findFirstQuery);
	if($findFirstStatement === false)
		throw new Exception("Query failed: $findFirstQuery");
	$first = $findFirstStatement->fetchColumn(0);
	
	if($first === false) // the total records count is less than $totalCount
	{
		// find first job id
		$findFirstQuery = "SELECT min(id) FROM batch_job_sep WHERE $conditions";
		$findFirstStatement = $pdo->query($findFirstQuery);
		if($findFirstStatement === false)
			throw new Exception("Query failed: $findFirstQuery");
		$first = $findFirstStatement->fetchColumn(0);
		if($first === false)
		{
			if(is_null($jobSubType))
				throw new Exception("No records found for job type $jobTypeConst");
			else
				throw new Exception("No records found for job type $jobTypeConst, sub type $jobSubType");
		}
		
		// recalculate total count
		$totalCountQuery = "SELECT COUNT(id) FROM batch_job_sep WHERE $conditions AND id > $first AND id <= $last";
		$totalCountStatement = $pdo->query($totalCountQuery);
		if($totalCountStatement === false)
			throw new Exception("Query failed: $totalCountQuery");
		$totalCount = $totalCountStatement->fetchColumn(0);
	}
	
	
	// find count of jobs with the the right status $status
	$countQuery = "SELECT COUNT(id) FROM batch_job_sep WHERE $conditions AND status = $status AND id > $first AND id <= $last";
	$countStatement = $pdo->query($countQuery);
	if($countStatement === false)
		throw new Exception("Query failed: $countQuery");
	$count = $countStatement->fetchColumn(0);
	$percent = round($count / ($totalCount / 100), 2);
	
	
	
	// done
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;;
	$monitorResult->value = $percent;
	if(is_null($jobSubType))
		$monitorResult->description = "Jobs type $jobTypeConst, status $statusConst percent: $percent%";
	else
		$monitorResult->description = "Jobs type $jobTypeConst, sub type $jobSubType, status $statusConst percent: $percent%";
	
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

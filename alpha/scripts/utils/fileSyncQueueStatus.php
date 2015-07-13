<?php

fclose(STDOUT);		// hide the log messages written to stdout

require_once(dirname(__FILE__).'/../bootstrap.php');

define('MAX_FILESYNC_ID_PREFIX', 'fileSyncMaxId-dc');
define('LAST_FILESYNC_ID_PREFIX', 'fileSyncLastId-worker');

$sizeSteps = array('=0' => 0, '<=5MB' => 5000000, '<=500MB' => 500000000, '>500MB' => -500000000);

function getIniFilePaths($baseDir)
{
	if(!is_dir($baseDir))
		return array($baseDir);

	$result = array();
	$d = dir($baseDir);

	while (false !== ($file = $d->read()))
	{
		if(preg_match('/\.ini$/', $file))
			$result[] = $baseDir . DIRECTORY_SEPARATOR . $file;
	}
	$d->close();

	return $result;
}

function implodeDirectoryFiles($sourceFiles, $dest)
{
	$content = '';
	foreach($sourceFiles as $fileName)
		$content .= file_get_contents($fileName) . "\n";

	file_put_contents($dest, $content);
}

function getFileSyncWorkers($iniDir)
{
	$configFileName = kEnvironment::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . 'fileSyncStatus-config.ini';
	@mkdir(dirname($configFileName), 0777, true);
	
	$filePaths = getIniFilePaths($iniDir);
	implodeDirectoryFiles($filePaths, $configFileName);
	
	$config = new Zend_Config_Ini($configFileName);
	$batchConfig = $config->toArray();
	
	$result = array();
	foreach ($batchConfig as $section)
	{
		if (!isset($section["id"]) || 
			!isset($section["scriptPath"]) || 
			strpos($section["scriptPath"], 'KAsyncFileSyncImportExe') === false)
		{
			continue;
		}
		
		$result[] = array('id' => $section["id"], 'name' => $section["friendlyName"], 'filter' => $section["filter"]);
	}
	return $result;
}

function getExcludeFileSyncMap()
{
	$result = array();
	$dcConfig = kConf::getMap("dc_config");
	if(isset($dcConfig['sync_exclude_types']))
	{
		foreach($dcConfig['sync_exclude_types'] as $syncExcludeType)
		{
			$configObjectType = $syncExcludeType;
			$configObjectSubType = null;

			if(strpos($syncExcludeType, ':') > 0)
				list($configObjectType, $configObjectSubType) = explode(':', $syncExcludeType, 2);

			// translate api dynamic enum, such as contentDistribution.EntryDistribution - {plugin name}.{object name}
			if(!is_numeric($configObjectType))
				$configObjectType = kPluginableEnumsManager::apiToCore('FileSyncObjectType', $configObjectType);

			// translate api dynamic enum, including the enum type, such as conversionEngineType.mp4box.Mp4box - {enum class name}.{plugin name}.{object name}
			if(!is_null($configObjectSubType) && !is_numeric($configObjectSubType))
			{
				list($enumType, $configObjectSubType) = explode('.', $configObjectSubType);
				$configObjectSubType = kPluginableEnumsManager::apiToCore($enumType, $configObjectSubType);
			}

			if(!isset($result[$configObjectType]))
				$result[$configObjectType] = array();

			if(!is_null($configObjectSubType))
				$result[$configObjectType][] = $configObjectSubType;
		}
	}

	return $result;
}

function formatFileSize($size) 
{
	if ($size >= 1<<30)
		return number_format($size / (1 << 30), 2) . "GB";
	if ($size >= 1<<20)
		return number_format($size / (1 << 20), 2) . "MB";
	if ($size >= 1<<10)
		return number_format($size / (1 << 10), 2) . "KB";
	return number_format($size) . " bytes";
}

function writeOutput($msg)
{
	fwrite(STDERR, $msg);
}

$iniDir = __DIR__ . '/../../../configurations/batch/';
if(isset($argc) && $argc > 2)
{
	$iniDir = $argv[2];
}

$keysCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
if (!$keysCache)
{
	die('failed to get keys cache');
}

// get the max id / last id
$maxId = $keysCache->get(MAX_FILESYNC_ID_PREFIX . kDataCenterMgr::getCurrentDcId());
writeOutput('Max id for dc ['.kDataCenterMgr::getCurrentDcId().'] is ['.$maxId."]\n");

$excludeFileSyncMap = getExcludeFileSyncMap();

FileSyncPeer::setUseCriteriaFilter(false);

$fileSyncWorkers = getFileSyncWorkers($iniDir);
foreach ($fileSyncWorkers as $fileSyncWorker)
{
	$workerId = $fileSyncWorker['id'];
	$workerName = $fileSyncWorker['name'];
	$filter = $fileSyncWorker['filter'];
	
	$lastId = $keysCache->get(LAST_FILESYNC_ID_PREFIX . $workerId);
	
	// build the base criteria
	$baseCriteria = new Criteria();
	
	$baseCriteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_PENDING);
	$baseCriteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
	$baseCriteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
	
	$idCriterion = $baseCriteria->getNewCriterion(FileSyncPeer::ID, $lastId - 100, Criteria::GREATER_THAN);
	$idCriterion->addAnd($baseCriteria->getNewCriterion(FileSyncPeer::ID, $maxId, Criteria::LESS_THAN));
	$baseCriteria->addAnd($idCriterion);

	// init size limits from filter
	$sizeLimits = array();
	if (isset($filter['fileSizeLessThanOrEqual']))
	{
		$sizeLimits[] = array($filter['fileSizeLessThanOrEqual'], Criteria::LESS_EQUAL);
	}
	
	if (isset($filter['fileSizeGreaterThanOrEqual']))
	{
		$sizeLimits[] = array($filter['fileSizeGreaterThanOrEqual'], Criteria::GREATER_EQUAL);
	}
	
	$prevLimit = null;
	$breakdownBySize = array();
	foreach ($sizeSteps as $sizeName => $sizeLimit)
	{	
		// add size limits from current step
		$curSizeLimits = $sizeLimits;
		if ($prevLimit)
		{
			$curSizeLimits[] = $prevLimit; 
		}
		
		if ($sizeLimit >= 0)
		{
			$curSizeLimits[] = array($sizeLimit, Criteria::LESS_EQUAL);
			$prevLimit = array($sizeLimit, Criteria::GREATER_THAN);
		}
		else
		{
			$curSizeLimits[] = array(-$sizeLimit, Criteria::GREATER_THAN);
			$prevLimit = null;
		}
		
		// build size criterion
		$c = clone $baseCriteria;
		
		$sizeCrit = null;
		foreach ($curSizeLimits as $sizeLimit)
		{
			list($value, $comparison) = $sizeLimit;
			if (!$sizeCrit)
			{
				$sizeCrit = $c->getNewCriterion(FileSyncPeer::FILE_SIZE, $value, $comparison);
			}
			else
			{
				$sizeCrit->addAnd($c->getNewCriterion(FileSyncPeer::FILE_SIZE, $value, $comparison));
			}				
		}
		if ($sizeCrit)
		{
			$c->addAnd($sizeCrit);
		}
		
		// select the count and size group by object type & sub type
		$c->addGroupByColumn(FileSyncPeer::OBJECT_TYPE);
		$c->addGroupByColumn(FileSyncPeer::OBJECT_SUB_TYPE);
		
		$c->addSelectColumn('COUNT(file_sync.ID)');
		$c->addSelectColumn('SUM(file_sync.FILE_SIZE)');
		
		foreach($c->getGroupByColumns() as $column)
			$c->addSelectColumn($column);
		
		$stmt = FileSyncPeer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		// count only file syncs that should be synced
		$count = 0;
		$size = 0;
		foreach ($rows as $row) 
		{
			$objectType = $row['OBJECT_TYPE'];
			$objectSubType = $row['OBJECT_SUB_TYPE'];
			
			if (isset($excludeFileSyncMap[$objectType]) && 
				(!count($excludeFileSyncMap[$objectType]) ||
				in_array($objectSubType, $excludeFileSyncMap[$objectType])))
			{
				continue;
			}

			$count += $row['COUNT(file_sync.ID)'];
			$size += $row['SUM(file_sync.FILE_SIZE)'];
		}

		if ($count)
		{
			$breakdownBySize[$sizeName] = array($count, $size);
		}
	}
	
	// get next file sync to process
	$c = clone $baseCriteria;
	$c->addAscendingOrderByColumn(FileSyncPeer::ID);
	$fileSync = FileSyncPeer::doSelectOne($c);
	
	// print the status
	writeOutput("Worker id [$workerId] name [$workerName] lastId [$lastId] maxId - lastId [".($maxId - $lastId)."]\n");
	foreach ($breakdownBySize as $sizeName => $curStat)
	{
		list($count, $size) = $curStat;
		writeOutput("\t$sizeName - count $count, size ".formatFileSize($size)."\n");
	}
	
	if ($fileSync)
	{
		writeOutput("\tnext file sync id [".$fileSync->getId()."] created at [".$fileSync->getCreatedAt()."]\n");
	}
}

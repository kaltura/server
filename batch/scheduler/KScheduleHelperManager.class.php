<?php
/**
 * Manages commands between the helper and the scheuler
 *
 * @package Scheduler
 */
class KScheduleHelperManager
{
	/**
	 * @return array
	 */
	public static function loadCommands()
	{
		$commandsDir = self::getCommandsDir();
		
		if (!is_dir($commandsDir))
			return array();

		$dh = opendir($commandsDir);
	    if(!$dh) 
			return array();
			
		$commands = array();
        while (($file = readdir($dh)) !== false) 
        {
        	if($file == '.' || $file == '..')
        		continue;
        		
        	if(!preg_match('/.\.cmd$/', $file))
        		continue;
        		
        	$commandsFilePath = "$commandsDir/$file";
        	if(filetype($commandsFilePath) == 'dir')
        		continue;
        		
        	$moreCommands = self::loadCommandsFile($commandsFilePath);
        	if(is_array($moreCommands))
	        	foreach($moreCommands as $command)
	        		$commands[] = $command;
        }
        closedir($dh);
		
		return $commands;
	}
	
	/**
	 * @return array
	 */
	public static function loadResultsCommandsFile()
	{
		$commandsFilePath = self::getCommandsResultsFilePath();
		return self::loadCommandsFile($commandsFilePath);
	}
	
	/**
	 * @return array
	 */
	public static function loadCommandsFile($commandsFilePath)
	{
		if(!file_exists($commandsFilePath))
			return null;

		// ranames the file to prevent the helper from editing it
		$tmp_path = $commandsFilePath . '.tmp';
		$res = @rename($commandsFilePath, $tmp_path);
		if(!$res)
			return null;
		
		// loads the commands
		$command_lines = file($tmp_path);
		unlink($tmp_path);
		
		$commands = array();
        if(is_array($command_lines) && count($command_lines))
			foreach($command_lines as $command_line)
				$commands[] = unserialize(base64_decode($command_line));
				
		return $commands;
	}
	
	public static function clearFilters()
	{
		$dirPath = self::getQueueFiltersDir();
		
		if (!is_dir($dirPath))
			return;

		$dh = opendir($dirPath);
	    if(!$dh) 
			return;
			
        while (($file = readdir($dh)) !== false) 
        {
        	if($file == '.' || $file == '..')
        		continue;
        		
        	if(!preg_match('/.\.flt$/', $file))
        		continue;
        		
        	$filePath = "$dirPath/$file";
        	if(filetype($filePath) == 'dir')
        		continue;
        		
        	@unlink($filePath);
        }
        closedir($dh);
	}
	
	/**
	 * @return array<KalturaWorkerQueueFilter>
	 */
	public static function loadFilters()
	{
		$filtersDir = self::getQueueFiltersDir();
		if (!is_dir($filtersDir))
			return array();

		$dh = opendir($filtersDir);
	    if(!$dh) 
			return array();
			
		$filters = array();
        while (($file = readdir($dh)) !== false) 
        {
        	if($file == '.' || $file == '..')
        		continue;
        		
        	if(!preg_match('/.\.flt$/', $file))
        		continue;
        		
        	$filterFilePath = "$filtersDir/$file";
        	if(filetype($filterFilePath) == 'dir')
        		continue;
        		
        	$data = file_get_contents($filterFilePath);
			$filters[] = unserialize(base64_decode($data));
        }
        closedir($dh);
		
		return $filters;
	}
	
	
	/**
	 * @param string $statusDirPath
	 * @return array
	 */
	public static function loadRunningBatches()
	{
		$statusDirPath = self::getCommandsDir();
		
		if (!is_dir($statusDirPath))
			return array();

		$dh = opendir($statusDirPath);
	    if(!$dh) 
			return array();
			
		$maxBatches = 20;
		
		$runningBatches = array();
        while ($maxBatches > 0 && ($file = readdir($dh)) !== false) 
        {
        	if($file == '.' || $file == '..')
        		continue;

        	$maxBatches--;
        	        		
        	if(!preg_match('/.\.run/', $file))
        		continue;
        		
        	list($workerName, $batchIndex, $run) = explode('.', $file);
        	
			$runningBatches[$workerName][$batchIndex] = file_get_contents($statusDirPath . DIRECTORY_SEPARATOR . $file);
        }
        closedir($dh);
		
		return $runningBatches;
	}

	/**
	 * @param string $workerName
	 * @param int $batchIndex
	 */
	public static function unlinkRunningBatch($workerName, $batchIndex)
	{
		$statusDirPath = self::getCommandsDir();
		@unlink("$statusDirPath/$workerName.$batchIndex.run");
	}


	/**
	 * @param string $workerName
	 * @param int $batchIndex
	 */
	public static function saveRunningBatch($workerName, $batchIndex)
	{
		$statusDirPath = self::getCommandsDir();
		file_put_contents("$statusDirPath/$workerName.$batchIndex.run", getmypid());
	}

	/**
	 * @param string $filtersFileName
	 * @param KalturaWorkerQueueFilter $filter
	 */
	public static function saveFilter($filtersFileName, KalturaWorkerQueueFilter $filter)
	{
		$data = base64_encode(serialize($filter));

		$filtersFilePath = self::getQueueFiltersDir() . DIRECTORY_SEPARATOR . $filtersFileName;
		file_put_contents($filtersFilePath, $data);
	}
	
	/**
	 * @param string $configName
	 * @return boolean whether the filter file exist
	 */
	public static function checkForFilter($configName) 
	{
		clearstatcache();
		$filtersFilePath = self::getQueueFiltersDir() . DIRECTORY_SEPARATOR . $configName . ".flt";
		return file_exists($filtersFilePath);
	}

	/**
	 * @param string $file
	 * @param array $commands
	 */
	public static function saveCommandsResults(array $commands)
	{
		return self::saveCommands(self::getCommandsResultsFilePath(), $commands);
	}

	/**
	 * @param string $file
	 * @param array $commands
	 */
	public static function saveCommand($file, array $commands)
	{
		$commandsFilePath = self::getCommandsDir() . DIRECTORY_SEPARATOR . $file;
		return self::saveCommands($commandsFilePath, $commands);
	}

	/**
	 * @param string $file
	 * @param array $commands
	 */
	public static function saveCommands($commandsFilePath, array $commands)
	{
		$data = '';
		foreach($commands as $command)
			$data .= base64_encode(serialize($command)) . "\n";
			
		file_put_contents($commandsFilePath, $data, FILE_APPEND);
	}

	/**
	 * @return string
	 */
	protected static function getCachePath()
	{
		$path = kEnvironment::get("cache_root_path") . DIRECTORY_SEPARATOR . 'batch';
		if(!file_exists($path))
			kFile::fullMkdir($path);
			
		return $path;
	}

	/**
	 * @return string
	 */
	protected static function getConfigItemsFilePath()
	{
		return self::getCachePath() . DIRECTORY_SEPARATOR . 'config.log';
	}

	/**
	 * @return string
	 */
	protected static function getStatusFilePath()
	{
		return self::getCachePath() . DIRECTORY_SEPARATOR . 'status.log';
	}

	/**
	 * @return string
	 */
	protected static function getCommandsResultsFilePath()
	{
		return self::getCachePath() . DIRECTORY_SEPARATOR . 'control.arc';
	}
	
	/**
	 * @return string
	 */
	protected static function getCommandsDir()
	{
		$path = self::getCachePath() . DIRECTORY_SEPARATOR . 'controls';
		if(!file_exists($path)) {
			kFile::fullMkfileDir($path);
		}
			
		return $path;
	}
	
	/**
	 * @return string
	 */
	protected static function getQueueFiltersDir()
	{
		$path = self::getCachePath() . DIRECTORY_SEPARATOR . 'filters';
		if(!file_exists($path))
			kFile::fullMkfileDir($path);
			
		return $path;
	}

	/**
	 * @return array<KalturaSchedulerStatus>
	 */
	public static function loadStatuses()
	{
		$filePath = self::getStatusFilePath();
		if(!file_exists($filePath))
			return array();

		// ranames the file to prevent the helper from editing it
		$tmp_path = $filePath . '.tmp';
		if(file_exists($tmp_path))
			unlink($tmp_path);
		rename($filePath, $tmp_path);
		
		// loads the commands
		$data = file_get_contents($tmp_path);
		unlink($tmp_path);
		
		if(!$data)
			return array();
			
		return unserialize(base64_decode($data));
	}

	/**
	 * @param array<KalturaSchedulerStatus> $statuses
	 */
	public static function saveStatuses(array $statuses)
	{
		$filePath = self::getStatusFilePath();
		$data = null;
		$statusesFromFile = array();
		if (file_exists($filePath))
			$data = file_get_contents($filePath);
		if($data)
			$statusesFromFile = unserialize(base64_decode($data));
		file_put_contents($filePath, base64_encode(serialize(array_merge($statusesFromFile,$statuses))), LOCK_EX);
	}

	/**
	 * @return array<KalturaSchedulerConfig>
	 */
	public static function loadConfigItems()
	{
		$filePath = self::getConfigItemsFilePath();
		if(!file_exists($filePath))
			return;

		// ranames the file to prevent the helper from editing it
		$tmp_path = $filePath . '.tmp';
		rename($filePath, $tmp_path);
		
		// loads the commands
		$configItem_lines = file($tmp_path);
		unlink($tmp_path);
		
		$configItems = array();
		foreach($configItem_lines as $configItem_line)
		{
			$configItems[] = unserialize(base64_decode($configItem_line));
		}
		
		return $configItems;
	}

	/**
	 * @param array<KalturaSchedulerConfig> $configItems
	 */
	public static function saveConfigItems(array $configItems)
	{
		$data = '';
		foreach($configItems as $configItem)
			$data .= base64_encode(serialize($configItem)) . "\n";
			
		$filePath = self::getConfigItemsFilePath();
		file_put_contents($filePath, $data, FILE_APPEND);
	}
}

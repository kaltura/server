<?php
require_once("bootstrap.php");
/**
 * Manages commands between the helper and the scheuler
 *
 * @package Scheduler
 */
class KScheduleHelperManager
{
	/**
	 * @param string $commandsDir
	 * @return array
	 */
	public static function loadCommands($commandsDir)
	{
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
	 * @param string $commandsFilePath
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
	
	/**
	 * @param string $filtersDir
	 * @return array<KalturaWorkerQueueFilter>
	 */
	public static function loadFilters($filtersDir)
	{
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
	public static function loadRunningBatches($statusDirPath)
	{
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
        	
			$runningBatches[$workerName][$batchIndex] = true;
        }
        closedir($dh);
		
		return $runningBatches;
	}

	/**
	 * @param string $statusDirPath
	 * @param string $workerName
	 * @param int $batchIndex
	 */
	public static function unlinkRunningBatch($statusDirPath, $workerName, $batchIndex)
	{
		@unlink("$statusDirPath/$workerName.$batchIndex.run");
	}


	/**
	 * @param string $statusDirPath
	 * @param string $workerName
	 * @param int $batchIndex
	 */
	public static function saveRunningBatch($statusDirPath, $workerName, $batchIndex)
	{
		file_put_contents("$statusDirPath/$workerName.$batchIndex.run", '', FILE_APPEND);
	}

	/**
	 * @param string $filtersFilePath
	 * @param KalturaWorkerQueueFilter $filter
	 */
	public static function saveFilter($filtersFilePath, KalturaWorkerQueueFilter $filter)
	{
		$data = base64_encode(serialize($filter));
			
		file_put_contents($filtersFilePath, $data);
	}

	/**
	 * @param string $commandsFilePath
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
	 * @param string $filePath
	 * @return array<KalturaSchedulerStatus>
	 */
	public static function loadStatuses($filePath)
	{
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
	 * @param string $filePath
	 * @param array<KalturaSchedulerStatus> $statuses
	 */
	public static function saveStatuses($filePath, array $statuses)
	{
		file_put_contents($filePath, base64_encode(serialize($statuses)), LOCK_EX);
	}

	/**
	 * @param string $filePath
	 * @return array<KalturaSchedulerConfig>
	 */
	public static function loadConfigItems($filePath)
	{
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
	 * @param string $filePath
	 * @param array<KalturaSchedulerConfig> $configItems
	 */
	public static function saveConfigItems($filePath, array $configItems)
	{
		$data = '';
		foreach($configItems as $configItem)
			$data .= base64_encode(serialize($configItem)) . "\n";
			
		file_put_contents($filePath, $data, FILE_APPEND);
	}
}

?>
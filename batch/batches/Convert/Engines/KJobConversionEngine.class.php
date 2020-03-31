<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion.engines
 */
require_once(__DIR__.'/../../../../alpha/apps/kaltura/lib/dateUtils.class.php');
require_once(__DIR__.'/../../../../alpha/apps/kaltura/lib/storage/kFileUtils.php');
abstract class KJobConversionEngine extends KConversionEngine
{
	/**
	 * @param KalturaConvertJobData $data
	 * @return array<KConversioEngineResult>
	 */
	protected function getExecutionCommandAndConversionString ( KalturaConvertJobData $data )
	{
		$tempPath = dirname($data->destFileSyncLocalPath);
		$this->logFilePath = $data->logFileSyncLocalPath;
		
		// assume there always will be this index
		$conv_params = $data->flavorParamsOutput;
 
		$cmd_line_arr = $this->getCmdArray($conv_params->commandLinesStr);

		$conversion_engine_result_list = array();
		
		foreach ( $cmd_line_arr as $type => $cmd_line )
		{
			if($type != $this->getType())
				continue;
				
			$cmdArr = explode(self::MILTI_COMMAND_LINE_SEPERATOR, $cmd_line);
			$lastIndex = count($cmdArr) - 1;
			
			foreach($cmdArr as $index => $cmd)
			{
				if($index == 0)
				{
					$this->inFilePath = $this->getSrcActualPathFromData($data);
				}
				else
				{
					$this->inFilePath = $this->outFilePath;
				}
			
				if($lastIndex > $index)
				{
					$uniqid = uniqid("tmp_convert_");
					$this->outFilePath = $tempPath . DIRECTORY_SEPARATOR . $uniqid;
				}
				else
				{
					$this->outFilePath = $data->destFileSyncLocalPath;	
				}
				
				$cmd = trim($cmd);
				if($cmd == self::FAST_START_SIGN)
				{
					$exec_cmd = $this->getQuickStartCmdLine(true);
				}
				else
				{
					$exec_cmd = $this->getCmdLine ( $cmd , true );
				}
				$conversion_engine_result = new KConversioEngineResult( $exec_cmd , $cmd );
				$conversion_engine_result_list[] = $conversion_engine_result;
			}	
		}
		
		return $conversion_engine_result_list;			
	}	
	
	public function simulate ( KalturaConvartableJobData $data )
	{
		return  $this->simulatejob ( $data );
	}	
	
	private function simulatejob ( KalturaConvertJobData $data )
	{
		return  $this->getExecutionCommandAndConversionString ( $data );
	}
	
	public function convert ( KalturaConvartableJobData &$data )
	{
		return  $this->convertJob ( $data );
	}
	
	public function convertJob ( KalturaConvertJobData &$data )
	{

		$error_message = "";  
		$actualFileSyncLocalPath = $this->getSrcActualPathFromData($data);
		if ( ! file_exists ( $actualFileSyncLocalPath ) )
		{
			$error_message = "File [{$actualFileSyncLocalPath}] does not exist";
			KalturaLog::err(  $error_message );
			return array ( false , $error_message );
		}

		if ( ! $data->logFileSyncLocalPath )
		{
			$data->logFileSyncLocalPath = $data->destFileSyncLocalPath . ".log";
		}
		
		$log_file = $data->logFileSyncLocalPath;
	
		// will hold a list of commands
		// there is a list (most probably holding a single command)
		// just incase there are multiple commands such as in FFMPEG's 2 pass
		$conversion_engine_result_list = $this->getExecutionCommandAndConversionString ( $data );
		
		$this->addToLogFile ( $log_file , "Executed by [" . $this->getName() . "] flavor params id [" . $data->flavorParamsOutput->flavorParamsId . "]" ) ;
		
		// add media info of source 
		$this->logMediaInfo ( $log_file , $actualFileSyncLocalPath );
		
		$duration = 0;
		foreach ( $conversion_engine_result_list as $conversion_engine_result )
		{
			$execution_command_str = $conversion_engine_result->exec_cmd;
			$conversion_str = $conversion_engine_result->conversion_string; 
			
			$this->addToLogFile ( $log_file , $execution_command_str ) ;
			$this->addToLogFile ( $log_file , $conversion_str ) ;
				
			KalturaLog::info ( $execution_command_str );
	
			$start = microtime(true);
			// TODO add BatchEvent - before conversion + conversion engine
			$output = $this->execute_conversion_cmdline($execution_command_str , $return_value , $data);
			// TODO add BatchEvent - after conversion + conversion engine		
			$end = microtime(true);
	
			// 	TODO - find some place in the DB for the duration
			$duration += ( $end - $start );
						 
			KalturaLog::info ( $this->getName() . ": [$return_value] took [$duration] seconds" );
			
			$this->addToLogFile ( $log_file , $output ) ;
			
			if ( $return_value != 0 ) 
				return array ( false , "return value: [$return_value]"  );
		}
		// add media info of target
		$this->logMediaInfo ( $log_file , $data->destFileSyncLocalPath );
		
		
		return array ( true , $error_message );// indicate all was converted properly
	}

	protected function isConversionProgressing($currentModificationTime)
	{
		if (kFile::checkFileExists($this->inFilePath))
		{
			$newModificationTime = kFile::getFileLastUpdatedTime($this->inFilePath);
			if ($newModificationTime !== false && $newModificationTime > $currentModificationTime)
			{
				return $newModificationTime;
			}
		}
		return false;
	}

	protected function getReturnValues($handle)
	{
		$return_var = 1;
		$output = false;
		if ($handle)
		{
			$return_var = 0;
			$file = $this->outFilePath;
			if (kFile::checkFileExists($file))
			{
				$output = kFile::getLineFromFileTail($file , 1);
			}
		}
		return array($output, $return_var);
	}

	/**
	 *
	 */
	protected function execute_conversion_cmdline($command, &$return_var, $data = null)
	{
		if (isset(KBatchBase::$taskConfig->params->usingSmartJobTimeout) && KBatchBase::$taskConfig->params->usingSmartJobTimeout == 1)
		{
			return $this->executeConversionCmdlineSmartTimeout($command, $return_var, $data);
		}
		else
		{
			$output = system($command, $return_var);
			return $output;
		}
	}

	protected function executeConversionCmdlineSmartTimeout($command, &$return_var, $data = null)
	{
		$flavorAsset = self::getFlavorFromData($data);
		$handle = popen($command, 'r');
		stream_set_blocking ($handle,0) ;
		$currentModificationTime = 0;
		$lastTimeOutSet = time();
		$maximumExecutionTime = KBatchBase::$taskConfig->maximumExecutionTime;
		$extendTime = $maximumExecutionTime ? ($maximumExecutionTime / 3) : dateUtils::HOUR;
		$timeout = $maximumExecutionTime;
		while(!feof($handle))
		{
			clearstatcache();
			$buffer = fread($handle,1);
			$newModificationTime = $this->isConversionProgressing($currentModificationTime);
			if($newModificationTime)
			{
				if ($lastTimeOutSet + $extendTime < time())
				{
					list($timeout, $lastTimeOutSet) = self::extendExpiration($flavorAsset, $maximumExecutionTime, $timeout);
					KalturaLog::debug('Previous modification time was:  ' . $currentModificationTime . ', new modification time is: '. $newModificationTime);
				}
				$currentModificationTime = $newModificationTime;
			}
			sleep(1);
			if(self::isReachedTimeout($timeout))
			{
				pclose($handle);
				$return_var = 1;
				return false;
			}
		}
		list($output, $return_var) = $this->getReturnValues($handle);
		pclose($handle);
		return $output;
	}


	protected static function getFlavorFromData($data)
	{
		$flavorAsset = null;
		if ($data && isset($data->flavorAssetId) && isset($data->flavorParamsOutput))
		{
			try
			{
				KBatchBase::impersonate($data->flavorParamsOutput->partnerId);
				$flavorAsset = KBatchBase::$kClient->flavorAsset->get($data->flavorAssetId);
				KBatchBase::unimpersonate();
			}
			catch (Exception $e)
			{
				KalturaLog::err('Flavor is not found. ' . $e->getMessage());
			}
		}
		return $flavorAsset;
	}

	protected static function extendExpiration($flavorAsset, $maximumExecutionTime, $timeout)
	{
		if ($flavorAsset)
		{
			try
			{
				KBatchBase::$kClient->batch->extendBatchJobLockExpiration($flavorAsset->id, $flavorAsset->entryId, $maximumExecutionTime);
				$timeout += $maximumExecutionTime;
			}
			catch (Exception $e)
			{
				KalturaLog::debug('Extend batch job lock failed. '. $e->getMessage());
			}
		}
		$lastTimeOutSet = time();
		return array($timeout, $lastTimeOutSet);
	}

	protected static function isReachedTimeout(&$timeout)
	{
		$timeout--;
		if($timeout <= 0)
		{
			KalturaLog::debug("Reached to TIMEOUT");
			return true;
		}
		return false;
	}
}



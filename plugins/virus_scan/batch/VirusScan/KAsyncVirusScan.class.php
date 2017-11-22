<?php
/**
 * Will scan for viruses on specified file  
 *
 * @package plugins.virusScan
 * @subpackage Scheduler
 */
class KAsyncVirusScan extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::VIRUS_SCAN;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->scan($job, $job->data);
	}
	
	protected function scan(KalturaBatchJob $job, KalturaVirusScanJobData $data)
	{
		try
		{
			$engine = VirusScanEngine::getEngine($job->jobSubType);
			if (!$engine)
			{
				KalturaLog::err('Cannot create VirusScanEngine of type ['.$job->jobSubType.']');
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, 'Error: Cannot create VirusScanEngine of type ['.$job->jobSubType.']', KalturaBatchJobStatus::FAILED);
				return $job;
			}
						
			// configure engine
			if (!$engine->config(self::$taskConfig->params))
			{
				KalturaLog::err('Cannot configure VirusScanEngine of type ['.$job->jobSubType.']');
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, 'Error: Cannot configure VirusScanEngine of type ['.$job->jobSubType.']', KalturaBatchJobStatus::FAILED);
				return $job;
			}
			
			$cleanIfInfected = $data->virusFoundAction == KalturaVirusFoundAction::CLEAN_NONE || $data->virusFoundAction == KalturaVirusFoundAction::CLEAN_DELETE;
			$errorDescription = null;
			$output = null;
			
			// execute scan
			$key = $data->fileContainer->encryptionKey;
			if (!$key)
				$data->scanResult = $engine->execute($data->fileContainer->filePath, $cleanIfInfected, $output, $errorDescription);
			else
			{
				$tempPath = self::createTempClearFile($data->fileContainer->filePath, $key);
				$data->scanResult = $engine->execute($tempPath, $cleanIfInfected, $output, $errorDescription);
				unlink($tempPath);
			}

			if (!$output) {
				KalturaLog::notice('Virus scan engine ['.get_class($engine).'] did not return any log for file ['.$data->srcFilePath.']');
				$output = 'Virus scan engine ['.get_class($engine).'] did not return any log';
			}
		
			try
			{
				self::$kClient->batch->logConversion($data->flavorAssetId, $output);
			}
			catch(Exception $e)
			{
				KalturaLog::err("Log conversion: " . $e->getMessage());
			}

			// check scan results
			switch ($data->scanResult)
			{
				case KalturaVirusScanJobResult::SCAN_ERROR:
					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, "Error: " . $errorDescription, KalturaBatchJobStatus::RETRY, $data);
					break;
				
				case KalturaVirusScanJobResult::FILE_IS_CLEAN:
					$this->closeJob($job, null, null, "Scan finished - file was found to be clean", KalturaBatchJobStatus::FINISHED, $data);
					break;
				
				case KalturaVirusScanJobResult::FILE_WAS_CLEANED:
					$this->closeJob($job, null, null, "Scan finished - file was infected but scan has managed to clean it", KalturaBatchJobStatus::FINISHED, $data);
					break;
					
				case KalturaVirusScanJobResult::FILE_INFECTED:
				
					$this->closeJob($job, null, null, "File was found INFECTED and wasn't cleaned!", KalturaBatchJobStatus::FINISHED, $data);
					break;
					
				default:
					$data->scanResult = KalturaVirusScanJobResult::SCAN_ERROR;
					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, "Error: Emtpy scan result returned", KalturaBatchJobStatus::RETRY, $data);
					break;
			}
			
		}
		catch(Exception $ex)
		{
			$data->scanResult = KalturaVirusScanJobResult::SCAN_ERROR;
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $data);
		}
		return $job;
	}
}

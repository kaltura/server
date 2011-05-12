<?php
require_once("bootstrap.php");
/**
 * Will export a single file to ftp or scp server 
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */
class KAsyncStorageSync extends KAsyncStorageExport
{
	/**
	 * Will take a single KalturaBatchJob and export the given file 
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaStorageExportJobData $data
	 * @return KalturaBatchJob
	 */
	protected function export(KalturaBatchJob $job, KalturaStorageExportJobData $data)
	{
		KalturaLog::debug("export($job->id)");
		
		$srcFile = str_replace('//', '/', trim($data->srcFileSyncLocalPath));
		$this->updateJob($job, "Syncing $srcFile, id: $data->srcFileSyncId", KalturaBatchJobStatus::QUEUED, 1);

		$remoteClientConfig = clone $this->kClient->getConfig();
		$remoteClientConfig->serviceUrl = $data->serverUrl;
		$remoteClientConfig->curlTimeout = $this->taskConfig->maximumExecutionTime;
		
		$remoteClient = new KalturaClient($remoteClientConfig);
		$remoteClient->setKs($this->kClient->getKs());
		
		try{
			$fileSync = $remoteClient->fileSync->sync($data->srcFileSyncId, realpath($srcFile));
			if($fileSync->status == KalturaFileSyncStatus::READY)
				return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
			
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, 0, 'File sync not ready', KalturaBatchJobStatus::RETRY);
		}
		catch(KalturaException $kex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::RETRY);
		}
		catch(KalturaClientException $kcex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
		}
		catch(Exception $e)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
	}
	
	/*
	 * @return string
	 */
	protected function getSupportedProtocols()
	{
		return KalturaExportProtocol::KALTURA_DC;
	}
}
?>
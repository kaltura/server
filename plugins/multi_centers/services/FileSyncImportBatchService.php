<?php
/**
 * @service filesyncImportBatch
 * @package plugins.multiCenters
 * @subpackage api.services
 */
class FileSyncImportBatchService extends KalturaBatchService
{


// --------------------------------- FileSyncImportJob functions 	--------------------------------- //


	/**
	 * batch getExclusiveFileSyncImportJob action allows to get a BatchJob of type FILESYNC_IMPORT
	 *
	 * @action getExclusiveFileSyncImportJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return.
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs
	 * @param int $maxOffset The maximum offset we accept for the distance from the best result.
	 * @return KalturaBatchJobArray
	 *
	 * TODO remove the destFilePath from the job data and get it later using the api, then delete this method
	 */
	function getExclusiveFileSyncImportJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $maxOffset = null)
	{
		$coreJobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::FILESYNC_IMPORT, $maxOffset);
		$jobs = KalturaBatchJobArray::fromBatchJobArray($coreJobs);

		if($jobs)
		{

			foreach ($jobs as $index => $job)
			{
				$data = $job->data;
				// try to get destination path from file sync
				$fileSyncId = $data->filesyncId;

				$fileSync = FileSyncPeer::retrieveByPK($fileSyncId);
				if (!$fileSync) {
					KalturaLog::err("Failed to load file sync [$fileSyncId] aborting job [{$job->id}]");
					$dbJob = BatchJobPeer::retrieveByPK($job->id);
					$dbJob->setMessage("Failed to load file sync [$fileSyncId]");
					kJobsManager::abortDbBatchJob($dbJob);
					unset($jobs[$index]);
					continue;
				}
				$fileSyncRoot = $fileSync->getFileRoot();
				$fileSyncPath = $fileSync->getFilePath();

				if ($fileSyncRoot && $fileSyncPath) {
					// destination path set on filesync
					$dest_path = $fileSyncRoot.$fileSyncPath;
				}
				else {
					// not set on filesync - get path from path manager
					$fileSyncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
					list($file_root, $real_path) = kPathManager::getFilePathArr($fileSyncKey);
					$dest_path = $file_root . $real_path;
					// update filesync on database
					$fileSync->setFileRoot($file_root);
					$fileSync->setFilePath($real_path);
					$fileSync->save();
				}

				// update job data with destination path if needed
				if (!$data->destFilePath) {
					$data->destFilePath = $dest_path;
					$job->data = $data;
					KalturaLog::log('Updating destination path for job id [$job->id]');
					$this->updateJob($job);
				}

				if (!is_dir(dirname($dest_path)) && !@mkdir(dirname($dest_path), 0755, true)) {
					KalturaLog::ERR("Cannot create directory [$dest_path] - ".error_get_last());
				}
			}
		}

		return $jobs;
	}

	private function updateJob(KalturaBatchJob $job)
	{
		$dbJob = BatchJobPeer::retrieveByPK($job->id);
		$dbJob = $job->toObject($dbJob);
		$dbJob->save();
	}

// --------------------------------- End of FileSyncImportJob functions 	------------------------ //



}

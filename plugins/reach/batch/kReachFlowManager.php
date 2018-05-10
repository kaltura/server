<?php
class kReachFlowManager implements kBatchJobStatusEventConsumer
{
	const SERVE_CSV_PARTIAL_URL = "/api_v3/index.php/service/reach_entryvendortask/action/serveCsv/ks/";

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == ReachPlugin::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV))
			return true;
				
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$dbBatchJobLock = $dbBatchJob->getBatchJobLock();
		try
		{
			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED || $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FATAL)	{
				kJobsManager::abortChildJobs($dbBatchJob);
			}

			$jobType = $dbBatchJob->getJobType();
			switch($jobType)
			{
				case ReachPlugin::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV):
					$dbBatchJob=$this->updatedEntryVendorTasksCsv($dbBatchJob, $dbBatchJob->getData());
					break;
				default:
					break;
			}

			if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY) {
				if($dbBatchJobLock && $dbBatchJobLock->getExecutionAttempts() >= BatchJobLockPeer::getMaxExecutionAttempts($jobType))
					$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FAILED);
			}

		}
		catch ( Exception $ex )
		{
			self::alert($dbBatchJob, $ex);
			KalturaLog::err( "Error:" . $ex->getMessage() );
		}

		return true;
	}

	// creates a mail job with the exception data
	protected static function alert(BatchJob $dbBatchJob, Exception $exception)
	{
		$jobData = new kMailJobData();
		$jobData->setMailPriority( kMailJobData::MAIL_PRIORITY_HIGH);
		$jobData->setStatus(kMailJobData::MAIL_STATUS_PENDING);

		KalturaLog::alert("Error in job [{$dbBatchJob->getId()}]\n".$exception);

		$jobData->setMailType(90); // is the email template
		$jobData->setBodyParamsArray(array($dbBatchJob->getId(), $exception->getFile(), $exception->getLine(), $exception->getMessage(), $exception->getTraceAsString()));

		$jobData->setFromEmail(kConf::get("batch_alert_email"));
		$jobData->setFromName(kConf::get("batch_alert_name"));
		$jobData->setRecipientEmail(kConf::get("batch_alert_email"));
		$jobData->setSubjectParamsArray( array() );

		kJobsManager::addJob($dbBatchJob->createChild(BatchJobType::MAIL, $jobData->getMailType()), $jobData, BatchJobType::MAIL, $jobData->getMailType());
	}

	protected function updatedEntryVendorTasksCsv(BatchJob $dbBatchJob, kEntryVendorTaskCsvJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::handleEntryVendorTasksCsvFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	public static function handleEntryVendorTasksCsvFinished(BatchJob $dbBatchJob, kEntryVendorTaskCsvJobData $data)
	{
		// Move file from shared temp to it's final location
		$fileName =  basename($data->getOutputPath());
		$directory =  myContentStorage::getFSContentRootPath() . "/content/entryVendorTasksCsv/" . $dbBatchJob->getPartnerId() ;
		if(!file_exists($directory))
			kFile::fullMkfileDir($directory);
		$filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

		if(!$data->getOutputPath())
			throw new APIException(APIErrors::FILE_CREATION_FAILED, "file path not found");

		KalturaLog::info("Trying to move entry vendor tasks csv file from: " . $data->getOutputPath() . " to: " . $filePath);
		try
		{
			kFile::moveFile($data->getOutputPath(), $filePath);
		}
		catch (Exception $e)
		{
			throw new APIException(APIErrors::FILE_CREATION_FAILED, $e->getMessage());
		}

		$data->setOutputPath($filePath);
		$dbBatchJob->setData($data);
		$dbBatchJob->save();

		KalturaLog::info("file path: [$filePath]");

		$downloadUrl = self::createEntryVendorTasksCsvDownloadUrl($dbBatchJob->getPartnerId(), $fileName);
		$userName = $data->getUserName();
		$bodyParams = array($userName, $downloadUrl);
		//send the created csv by mail
		kJobsManager::addMailJob(
			null,
			0,
			$dbBatchJob->getPartnerId(),
			ReachMailType::MAIL_TYPE_ENTRY_VENDOR_TASKS_CSV,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get("partner_notification_email"),
			kConf::get("partner_notification_name"),
			$data->getUserMail(),
			$bodyParams
		);

		return $dbBatchJob;
	}


	protected static function createEntryVendorTasksCsvDownloadUrl ($partner_id, $file_name)
	{
		$ksStr = "";
		$partner = PartnerPeer::retrieveByPK ($partner_id);
		$secret = $partner->getSecret ();
		$privilege = ks::PRIVILEGE_DOWNLOAD . ":" . $file_name;
		//ks will expire after 24 hours
		$expiry = 86400;
		$result = kSessionUtils::startKSession($partner_id, $secret, null, $ksStr, $expiry, false, "", $privilege);

		if ($result < 0)
			throw new APIException(APIErrors::START_SESSION_ERROR, $partner);

		//url is built with DC url in order to be directed to the same DC of the saved file
		$url = kDataCenterMgr::getCurrentDcUrl() . self::SERVE_CSV_PARTIAL_URL ."$ksStr/id/$file_name";

		return $url;
	}

}
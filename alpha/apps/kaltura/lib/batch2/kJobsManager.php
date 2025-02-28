<?php

/**
 * 
 * Manages the jobs add, get status and abort
 * 
 * @package Core
 * @subpackage Batch
 *
 */
class kJobsManager
{
	
	const BULK_DOWNLOAD_TOTAL_ENTRIES_AMOUNT_RESTRICTION = 1000;
	
	const BULK_DOWLOAD_SINGLE_JOB_ENTRIES_AMOUNT = 100;
	
	// helper function for setting the error description and status of a batchJob
	public static function failBatchJob(BatchJob $batchJob, $errDescription)
	{
		$batchJob->setMessage($errDescription);
		$description = $batchJob->getDescription() . "\n$errDescription";
		$batchJob->setDescription($description);
		return self::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_FAILED);
	}
	
	/**
	 * @param BatchJob $batchJob
	 * @param int $status
	 * @return BatchJob
	 */
	public static function updateBatchJob(BatchJob $batchJob, $status)
	{
		$batchJob->setStatus($status);
		$changed = $batchJob->save();
		
		if(!$changed) {
			return $batchJob;
		}
		
		$event = new kBatchJobStatusEvent($batchJob);
		kEventsManager::raiseEvent($event);
		$batchJob->reload();
		return $batchJob;
	}
	
	public static function abortEntryJobs($entryId)
	{
		$dbBatchJobLocks = BatchJobLockPeer::retrieveByEntryId($entryId);
		
		foreach($dbBatchJobLocks as $dbBatchJobLock) {
			/* @var $dbBatchJobLock BatchJobLock */
			$dbBatchJob = $dbBatchJobLock->getBatchJob();
			if($dbBatchJob!==null)
			{
				$dbBatchJob->setMessage("Aborted entry");
				self::abortDbBatchJob($dbBatchJob);
			}
		}
	}
	
	public static function abortJob($jobId, $jobType, $force = false)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($jobId);
		if($dbBatchJob->getJobType() != $jobType)
			throw new APIException(APIErrors::GET_EXCLUSIVE_JOB_WRONG_TYPE, $jobType, $dbBatchJob->getId());
			
		return self::abortDbBatchJob($dbBatchJob, $force);
	}
	
	public static function deleteJob($jobId, $jobType)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($jobId);
		if($dbBatchJob->getJobType() != $jobType)
			throw new APIException(APIErrors::GET_EXCLUSIVE_JOB_WRONG_TYPE, $jobType, $dbBatchJob->getId());
			
		$dbBatchJob->setDeletedAt(time());
		$dbBatchJob->save();
		
		return $dbBatchJob;
	}
	
	public static function abortDbBatchJob(BatchJob $dbBatchJob, $force = false)
	{
		// No need to abort finished job
		if(in_array($dbBatchJob->getStatus(), BatchJobPeer::getClosedStatusList())) {
			if($force) {
				$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::ABORTED);
				$dbBatchJob->save();
			}
			return $dbBatchJob;
		}
		
		$lockObject = $dbBatchJob->getBatchJobLock();
		if(is_null($lockObject)) {
			KalturaLog::err("Batch job [" . $dbBatchJob->getId() . "] doesn't have a lock object and can't be deleted. Status (" . $dbBatchJob->getStatus() . ")");
			return $dbBatchJob;
		}
		
		// Update status 
		$con = Propel::getConnection();
		$update = new Criteria();
		$update->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_ABORTED);
		$update->add(BatchJobLockPeer::VERSION, $lockObject->getVersion() + 1);
		
		$updateCondition = new Criteria();
		$updateCondition->add(BatchJobLockPeer::ID, $lockObject->getId(), Criteria::EQUAL);
		$updateCondition->add(BatchJobLockPeer::VERSION, $lockObject->getVersion(), Criteria::EQUAL);
		$updateCondition->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
		
		$affectedRows = BasePeer::doUpdate($updateCondition, $update, $con);
		
		if($affectedRows) {
			$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::ABORTED);
			$dbBatchJob = self::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_ABORTED);
		} else {
				$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::ABORTED);
				$dbBatchJob->save();
		}
		
		self::abortChildJobs($dbBatchJob);
		return $dbBatchJob;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 */
	public static function abortChildJobs(BatchJob $dbBatchJob)
	{
		$maxJobsForQuery = 100;
		$c = new Criteria();
		$c->add(BatchJobPeer::STATUS, BatchJobPeer::getClosedStatusList(), Criteria::NOT_IN);
		$c->setLimit($maxJobsForQuery);
		// aborts all child jobs in chunks
		while(true) {
			$dbChildJobs = $dbBatchJob->getChildJobs($c);
		foreach($dbChildJobs as $dbChildJob) {
			$dbChildJob->setMessage("Parent job was aborted.");
			if($dbChildJob->getId() != $dbBatchJob->getId())
				self::abortDbBatchJob($dbChildJob);
		}
			if(count($dbChildJobs) < $maxJobsForQuery)
				break;
	}
	}
	/**
	 * @param int $jobId
	 * @param int $jobType
	 * @param bool $force - forces retry even if the job is locked.
	 * @return BatchJob
	 */
	public static function retryJob($jobId, $jobType, $force = false)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($jobId);
		if($dbBatchJob->getJobType() != $jobType) {
			throw new APIException(APIErrors::GET_EXCLUSIVE_JOB_WRONG_TYPE, $jobType, $dbBatchJob->getId());
		}
			
		$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::NORMAL);
		
		// if not currently locked
		$dbBatchJobLock = $dbBatchJob->getBatchJobLock();
		if($dbBatchJobLock === null) {
			
			// retry of non locked entry
			$dbBatchJob = self::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_RETRY);
			
			
			
		} elseif ($force) {
			
			// retry of scheduled entry
			$dbBatchJobLock->setExecutionAttempts(0);
			$dbBatchJobLock->setStatus(BatchJob::BATCHJOB_STATUS_RETRY);
			$dbBatchJobLock->setExpiration(time() + BatchJobLockPeer::getRetryInterval($jobType));
			$dbBatchJob->setStatus(BatchJob::BATCHJOB_STATUS_RETRY);
			$dbBatchJob->save();
		}
		
		return $dbBatchJob;
	}
	
	/**
	 * @param int $entryId
	 */
	public static function boostEntryJobs($entryId)
	{
		$entrydb = entryPeer::retrieveByPK($entryId);
		if(!$entrydb) {
			throw new APIException(APIErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		//Retrieve all batch jobs associated to the entry
		$batchJobs = BatchJobLockPeer::retrieveByEntryId($entryId);
		
		foreach($batchJobs as $job)
		{
			/* @var $job BatchJobLock */
			//Boost the job by setting priority and urjeny to 1 
			$job->setPriority(1);
			$job->setUrgency(1);
			$job->save();
		}
	}
	
	public static function addDynamicEmailJob($partnerId, $mailType, $mailPriority, $loginEmail, $fromMail, $fromName, $dynamicEmailContents)
	{
		return self::addMailJob(
			null,
			0,
			$partnerId,
			$mailType,
			$mailPriority,
			kConf::get ($fromMail ),
			kConf::get ($fromName ),
			$loginEmail,
			array(),
			array(),
			null,
			null,
			null,
			null,
			null,
			$dynamicEmailContents
		);
	}
	
	public static function addMailJob(
		BatchJob $parentJob = null, $entryId, $partnerId, $mailType, $mailPriority, $fromEmail, $fromName, $toEmail, array $bodyParams = array(),
		array $subjectParams = array(), $toName = null, $toId = null, $camaignId = null, $templatePath = null, $separator = null, $dynamicEmailContents = null)
	{
	  	$jobData = new kMailJobData();
		$jobData->setMailPriority($mailPriority);
	 	$jobData->setMailType($mailType);
		
	 	$jobData->setFromEmail($fromEmail);
	 	$jobData->setFromName($fromName);
	 	
		if ($separator)
			$jobData->setSeparator($separator);
		
	 	$jobData->setBodyParamsArray($bodyParams);
		$jobData->setSubjectParamsArray($subjectParams);
		
		$jobData->setRecipientEmail($toEmail);
		$jobData->setRecipientName($toName);
		$jobData->setRecipientId($toId);
		
		$jobData->setCampaignId($camaignId);		
		$jobData->setCampaignId($camaignId);
	 	$jobData->setTemplatePath($templatePath);
	
	 	$partner = PartnerPeer::retrieveByPK($partnerId);
		$jobData->setLanguage($partner->getLanguage());
		$jobData->setDynamicEmailContents($dynamicEmailContents);
	 	
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::MAIL, $mailType);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		return self::addJob($batchJob, $jobData, BatchJobType::MAIL, $mailType);
	}
	
	public static function addProvisionDeleteJob(BatchJob $parentJob = null, entry $entry)
	{
		$jobData = kProvisionJobData::getInstance($entry->getSource());
		if ($jobData){
			$jobData->setStreamID($entry->getStreamRemoteId());
			$subType = $entry->getSource();
			$jobData->populateFromPartner($entry->getPartner());
			$jobData->populateFromEntry($entry);
			$batchJob = null;
			if($parentJob)
			{
				$batchJob = $parentJob->createChild(BatchJobType::PROVISION_DELETE, $subType);
			}
			else
			{
				$batchJob = new BatchJob();
				$batchJob->setEntryId($entry->getId());
				$batchJob->setPartnerId($entry->getPartnerId());
			}
			
			$batchJob->setObjectId($entry->getId());
			$batchJob->setObjectType(BatchJobObjectType::ENTRY);
			return self::addJob($batchJob, $jobData, BatchJobType::PROVISION_DELETE, $subType);
		}
		return false;
	}
	
	public static function addProvisionProvideJob(BatchJob $parentJob = null, entry $entry, kProvisionJobData $jobData)
	{
		$entry->setStatus(entryStatus::IMPORT);
		$entry->save();
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::PROVISION_PROVIDE, $entry->getSource());
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entry->getId());
			$batchJob->setPartnerId($entry->getPartnerId());
		}
				
		$batchJob->setObjectId($entry->getId());
		$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		return self::addJob($batchJob, $jobData, BatchJobType::PROVISION_PROVIDE, $entry->getSource());
	}

	/**
	 * addConvertIsmCollectionJob creates a convert collection job 
	 * 
	 * @param string $tag 
	 * @param FileSyncKey $srcSyncKey
	 * @param entry $entry
	 * @param BatchJob $parentJob
	 * @param array<flavorParamsOutput> $flavorParamsOutputs
	 * @return BatchJob
	 */
	public static function addConvertIsmCollectionJob($tag, FileSyncKey $srcSyncKey, entry $entry, BatchJob $parentJob = null, array $flavorParamsOutputs, $sameRoot = null)
	{		
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		
		$srcFileSyncDescriptor = new kSourceFileSyncDescriptor();
		if($fileSync)
		{
			if($fileSync->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_URL)			
				$srcFileSyncDescriptor->setPathAndKeyByFileSync($fileSync);
			$srcFileSyncDescriptor->setFileSyncRemoteUrl($fileSync->getExternalUrl($entry->getId()));
			$srcFileSyncDescriptor->setAssetId($fileSync->getObjectId());			
			$srcFileSyncDescriptor->setFileSyncObjectSubType($srcSyncKey->getObjectSubType());		
		}
		
		// increment entry version
		$ismVersion = $entry->incrementIsmVersion();
		$entry->save();
		
		$fileName = $entry->generateFileName(0, $ismVersion);
		
		
		// creates convert data
		$convertCollectionData = new kConvertCollectionJobData();
		$convertCollectionData->setSrcFileSyncs(array($srcFileSyncDescriptor));
		$convertCollectionData->setDestFileName($fileName);
		
		$clipOffset = null;
		$clipDuration = null;
		
		// look for clipping params
		foreach($flavorParamsOutputs as $flavorParamsOutput){
			$clipOffset = $flavorParamsOutput->getClipOffset();
			$clipDuration = $flavorParamsOutput->getClipDuration();
			if(isset($clipOffset) || isset($clipDuration)){
				KalturaLog::log("Found clipping params: clipOffset($clipOffset),clipDuration($clipDuration)");
				break;
			}
		}

	
		// check bitrates duplications & update clipping params
		foreach($flavorParamsOutputs as $flavorParamsOutputIndex => $flavorParamsOutput)
		{

			// if one of clip params exsits - update the object and db
			if(isset($clipOffset)){
				$flavorParamsOutputs[$flavorParamsOutputIndex]->setClipOffset($clipOffset);
			}
			if(isset($clipDuration)){
				$flavorParamsOutputs[$flavorParamsOutputIndex]->setClipDuration($clipDuration);
			}
			if(isset($clipOffset) || isset($clipDuration)){
				$flavorParamsOutputs[$flavorParamsOutputIndex]->save();
			}
		}
			/*
			 * Put together all separted flavor XML's into a single Smooth Streaming preset file
			 */
				
				
		KalturaLog::log("Calling CDLProceessFlavorsForCollection with [" . count($flavorParamsOutputs) . "] flavor params");
				
		$presetXml = KDLWrap::CDLProceessFlavorsForCollection($flavorParamsOutputs);
		$presetXml = str_replace(KDLCmdlinePlaceholders::OutFileName, $fileName, $presetXml);

		
		foreach($flavorParamsOutputs as $flavorParamsOutput)
		{
			/*
			 * Save in case that videoBitrate was changed by the FlavorsForCollection (see above)
			 */
			$flavorParamsOutput->save();
			$convertCollectionFlavorData = new kConvertCollectionFlavorData();
			$convertCollectionFlavorData->setFlavorAssetId($flavorParamsOutput->getFlavorAssetId());
			$convertCollectionFlavorData->setFlavorParamsOutputId($flavorParamsOutput->getId());
			$convertCollectionFlavorData->setReadyBehavior($flavorParamsOutput->getReadyBehavior());
			$convertCollectionFlavorData->setVideoBitrate($flavorParamsOutput->getVideoBitrate());
			$convertCollectionFlavorData->setAudioBitrate($flavorParamsOutput->getAudioBitrate());
			$convertCollectionFlavorData->setAudioBitrate($flavorParamsOutput->getAudioBitrate());
			
			$convertCollectionData->addFlavor($convertCollectionFlavorData);
		}
		
		$currentConversionEngine = conversionEngineType::EXPRESSION_ENCODER3;
		KalturaLog::log("Using conversion engine [$currentConversionEngine]");
		
		if($sameRoot == null)
		{
			// creats a child convert job
			if($parentJob)
			{
				$dbConvertCollectionJob = $parentJob->createChild(BatchJobType::CONVERT_COLLECTION, $currentConversionEngine);
				KalturaLog::log("Created from parent convert job with entry id [" . $dbConvertCollectionJob->getEntryId() . "]");
			}
			else
			{
				$dbConvertCollectionJob = new BatchJob();
				$dbConvertCollectionJob->setEntryId($entry->getId());
				$dbConvertCollectionJob->setPartnerId($entry->getPartnerId());
				$dbConvertCollectionJob->setJobType(BatchJobType::CONVERT_COLLECTION);
				$dbConvertCollectionJob->setJobSubType($currentConversionEngine);
			}
		} else {
			$dbConvertCollectionJob = $parentJob->createChild(BatchJobType::CONVERT_COLLECTION, $currentConversionEngine, false);
		}
		
		$dbConvertCollectionJob->setObjectId($entry->getId());
		$dbConvertCollectionJob->setObjectType(BatchJobObjectType::ENTRY);
		$dbConvertCollectionJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$dbConvertCollectionJob = kJobsManager::addJob($dbConvertCollectionJob, $convertCollectionData, 
				BatchJobType::CONVERT_COLLECTION, $currentConversionEngine);
		
		
		$syncKey = $dbConvertCollectionJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG);
		kFileSyncUtils::file_put_contents($syncKey, $presetXml);
		
		$fileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey);
		$remoteUrl = $fileSync->getExternalUrl($entry->getId());
		$localPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		
		$commandLines = array(
				conversionEngineType::EXPRESSION_ENCODER3 => KDLCmdlinePlaceholders::InFileName . ' ' . KDLCmdlinePlaceholders::ConfigFileName,
		);
		$commandLinesStr = flavorParamsOutput::buildCommandLinesStr($commandLines);
		
		$convertCollectionData->setInputXmlLocalPath($localPath);
		$convertCollectionData->setInputXmlRemoteUrl($remoteUrl);
		$convertCollectionData->setCommandLinesStr($commandLinesStr);
		
		$dbConvertCollectionJob->setData($convertCollectionData);
		return kJobsManager::updateBatchJob($dbConvertCollectionJob, BatchJob::BATCHJOB_STATUS_PENDING);
	}
	
	
	/**
	 * addFlavorConvertJob adds a single flavor conversion 
	 * 
	 * @param FileSyncKey $srcSyncKey
	 * @param flavorParamsOutput $flavor
	 * @param int $flavorAssetId
	 * @param int $conversionProfileId
	 * @param int $mediaInfoId
	 * @param BatchJob $parentJob
	 * @param int $lastEngineType  
	 * @param bool $sameRoot
	 * @return BatchJob 
	 */
	public static function addFlavorConvertJob(array $srcSyncKeys, flavorParamsOutput $flavor, $flavorAssetId, $conversionProfileId = null,
			$mediaInfoId = null, BatchJob $parentJob = null, $lastEngineType = null, $sameRoot = true, $priority = 0)
	{
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		if(!$flavorAsset)
		{
			KalturaLog::err("No flavor asset found for id [$flavorAssetId]");
			return null;
		}
		$partner = PartnerPeer::retrieveByPK($flavorAsset->getPartnerId());
		$srcFileSyncs = array();
		$firstValidFileSync = null;
		
		// creates convert data
		$convertData = new kConvertJobData();
		$convertData->setMediaInfoId($mediaInfoId);
		$convertData->setFlavorParamsOutputId($flavor->getId());
		$convertData->setFlavorAssetId($flavorAssetId);
		$convertData->setConversionProfileId($conversionProfileId);
		$convertData->setPriority($priority);
		
		$dbCurrentConversionEngine = self::getNextConversionEngine($flavor, $parentJob, $lastEngineType, $convertData);
		if(!$dbCurrentConversionEngine)
			return null;
		
		foreach ($srcSyncKeys as $srcSyncKey) 
		{		
			$srcFileSyncDescriptor = new kSourceFileSyncDescriptor();
			$addImportJob = false;
			
			$fileSync = null;
			$preferSharedDcForConvert = kConf::get('prefer_shared_file_sync_for_convert', 'cloud_storage', null);
			$remoteConvertSupportedEngines =  kConf::get('remote_convert_supported_engines', 'cloud_storage', array(KalturaConversionEngineType::CHUNKED_FFMPEG));
			
			$partnerRemoteConvertSupportedEngines =  kConf::get('partner_remote_convert_supported_engines', 'cloud_storage', null);
			if($partnerRemoteConvertSupportedEngines && isset($partnerRemoteConvertSupportedEngines[$partner->getId()]))
			{
				$remoteConvertSupportedEngines = explode("," ,$partnerRemoteConvertSupportedEngines[$partner->getId()]);
			}
			
			$sharedDcIds = kDataCenterMgr::getSharedStorageProfileIds($partner->getId());
			if( ($preferSharedDcForConvert && count($sharedDcIds) && in_array($dbCurrentConversionEngine, $remoteConvertSupportedEngines)) )
			{
				$fileSync = kFileSyncUtils::getReadyFileSyncForKeyAndDc($srcSyncKey, $sharedDcIds);
			}
			
			if(!$fileSync)
			{
				$fileSync = self::getFileSyncForKey($srcSyncKey, $flavor, $flavorAsset, $partner, $addImportJob);
			}
			
			if(!$fileSync)
			{
				return null;
			}
				
			$srcFlavorAsset = assetPeer::retrieveById($srcSyncKey->getObjectId());
			if($addImportJob)
			{
				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
				$flavorAsset->setDescription("Source file sync is importing: $srcSyncKey");
				$flavorAsset->save();
				$url = $fileSync->getExternalUrl($flavorAsset->getEntryId(), null, true);
				return kJobsManager::addImportJob($parentJob, $flavorAsset->getEntryId(), $partner->getId(), $url, $srcFlavorAsset, null, null, true);
			}
			else 
			{
				if($flavor->getSourceRemoteStorageProfileId() == StorageProfile::STORAGE_KALTURA_DC)
				{
					if($fileSync->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_URL)
						$srcFileSyncDescriptor->setPathAndKeyByFileSync($fileSync);						
				}
				else
				{
					$srcFileSyncDescriptor->setPathAndKeyByFileSync($fileSync);
				}
				$srcFileSyncDescriptor->setFileSyncRemoteUrl($fileSync->getExternalUrl($flavorAsset->getEntryId(), null, true));
				$srcFileSyncDescriptor->setAssetId($srcSyncKey->getObjectId());
				$srcFileSyncDescriptor->setAssetParamsId($srcFlavorAsset->getFlavorParamsId());
				$srcFileSyncDescriptor->setFileSyncObjectSubType($srcSyncKey->getObjectSubType());
				$srcFileSyncs[] = $srcFileSyncDescriptor;
				$firstValidFileSync = $firstValidFileSync ? $firstValidFileSync : $fileSync;
			}
		}

		if (!self::shouldExeConvertJob($firstValidFileSync))
			return null;
		
		//Set convert src file syns
		$currentSrcFileSyncs = $convertData->getSrcFileSyncs() ? $convertData->getSrcFileSyncs() : array();
		$convertData->setSrcFileSyncs(array_merge($srcFileSyncs, $currentSrcFileSyncs));

		$sharedStorageProfileId = kDataCenterMgr::getSharedStorageProfileIds($partner->getId(), true);
		if($sharedStorageProfileId && self::shouldUseSharedStorageForEngine($dbCurrentConversionEngine))
		{
			$convertData->setDestFileSyncSharedPath(self::getSharedPath($partner,$parentJob,$flavorAsset));
		}
		
		// creats a child convert job
		if($parentJob)
		{
			$dbConvertFlavorJob = $parentJob->createChild( BatchJobType::CONVERT, $dbCurrentConversionEngine, $sameRoot);
		}
		else
		{
			$dbConvertFlavorJob = new BatchJob();
			$dbConvertFlavorJob->setPartnerId($flavor->getPartnerId());
			$dbConvertFlavorJob->setJobType(BatchJobType::CONVERT);
			$dbConvertFlavorJob->setJobSubType($dbCurrentConversionEngine);
		}
		$dbConvertFlavorJob->setEntryId($flavor->getEntryId());
		KalturaLog::log("Job created with entry id [" . $dbConvertFlavorJob->getEntryId() . "]");
		
		$mediaInfo = mediaInfoPeer::retrieveByPK($mediaInfoId);
		if($mediaInfo === NULL) {
			// in case we don't know the estimatted info, we will set it to a big number.
			$estimatedEffort = kJobData::MAX_ESTIMATED_EFFORT; 
		} else {
			$estimatedEffort = max($mediaInfo->getVideoDuration(),$mediaInfo->getAudioDuration(),$mediaInfo->getContainerDuration());
		}
		
		$dbConvertFlavorJob->setObjectId($flavorAssetId);
		$dbConvertFlavorJob->setObjectType(BatchJobObjectType::ASSET);
		$convertData->setEstimatedEffort($convertData->calculateEstimatedEffort($dbConvertFlavorJob));
		return kJobsManager::addJob($dbConvertFlavorJob, $convertData, BatchJobType::CONVERT, $dbCurrentConversionEngine);
	}
	
	private static function getFileSyncForKey(FileSyncKey $srcSyncKey, flavorParamsOutput $flavor, asset $flavorAsset, Partner $partner, &$addImportJob)
	{
		$addImportJob = false;
		$isLocal = ($flavor->getSourceRemoteStorageProfileId() == StorageProfile::STORAGE_KALTURA_DC);
		
		if($isLocal)
		{
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		}
		else 
		{
			$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($srcSyncKey, $flavor->getSourceRemoteStorageProfileId());
		}
		
		if(!$fileSync)
		{
			kBatchManager::updateEntry($flavorAsset->getEntryId(), entryStatus::ERROR_CONVERTING);
			if($isLocal)
				$description = "Source file sync not found: $srcSyncKey";
			else 
				$description = "Remote source file sync not found $srcSyncKey, storage profile id [" . $flavor->getSourceRemoteStorageProfileId() . "]";
			
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->setDescription($description);
			$flavorAsset->save();
				
			KalturaLog::err($description);
			return null;
		}
		
		if($isLocal && !$local)
		{		
			if(StorageProfile::shouldImportFile($fileSync, $partner))
				$addImportJob = true;
			else	
				throw new kCoreException("Source file not found for flavor conversion [" . $flavorAsset->getId() . "]", kCoreException::SOURCE_FILE_NOT_FOUND);
		}
		
		return $fileSync;		
	}
	
	private static function getSharedPath ($partner,$parentJob,$flavorAsset)
	{
			$sharedStorageProfile = StorageProfilePeer::retrieveByPK(kDataCenterMgr::getSharedStorageProfileIds($partner->getId(), true));
			$pathMgr = $sharedStorageProfile->getPathManager();
			//When convert is done we call incrementVersion so when creating the path we need to make sure path version is correct
			$nextVersion = $newVersion = kFileSyncUtils::calcObjectNewVersion($flavorAsset->getId(), $flavorAsset->getVersion(), FileSyncObjectType::ASSET, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			list($root, $path) = $pathMgr->generateFilePathArr($flavorAsset, asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $nextVersion);
			if($parentJob && $parentJob->getJobType()== BatchJobType::CLIP_CONCAT)
			{
				$temp_storage_bucket = kConf::get('temp_storage_bucket', myCloudUtils::CLOUD_STORAGE_MAP, null);
				if($temp_storage_bucket)
				{
					$root = $temp_storage_bucket;
				}
			}
			
			return kFile::fixPath(rtrim($root, "/") . DIRECTORY_SEPARATOR . ltrim($path, "/"));
	}
	
	private static function getNextConversionEngine(flavorParamsOutput $flavor, BatchJob $parentJob = null, $lastEngineType, kConvertJobData &$convertData)
	{
		KalturaLog::log("Conversion engines string: '" . $flavor->getConversionEngines() . "'");
		
		$currentConversionEngine = null;
		
		// TODO remove after all old version flavors migrated
		// parse supported engine types
		$conversionEngines = array();
		if(!$flavor->getEngineVersion()) // uses the old engine version
		{
			$conversionEngines = explode(',', $flavor->getConversionEngines());
			KalturaLog::log(count($conversionEngines) . " conversion engines found for the flavor");
			$currentConversionEngine = reset($conversionEngines); // gets the first engine type
		}
		// remove until here
		
		
		if(is_null($lastEngineType))
		{
			KalturaLog::log("Last Engine Type is null, engine version [" . $flavor->getEngineVersion() . "]");
			if($flavor->getEngineVersion()) // uses the new engine version
			{
				$operatorSet = new kOperatorSets();
				$operatorSet->setSerialized(/*stripslashes*/($flavor->getOperators()));
				$nextOperator = $operatorSet->getOperator();
				if(!$nextOperator)
				{
					KalturaLog::err("First operator is invalid");
					return null;
				}
				
				KalturaLog::log("Set first operator in first set");
				$currentConversionEngine = $nextOperator->id;
			}
		}
		else
		{
			if(
				$parentJob && 
				$flavor->getEngineVersion() &&
				(
					$parentJob->getJobType() == BatchJobType::CONVERT
					||
					$parentJob->getJobType() == BatchJobType::POSTCONVERT
				)
			) // uses the new engine version
			{
				// using next oprator
				KalturaLog::log("Adding next conversion operator");
				
				$parentData = $parentJob->getData();
				if(!$parentData || !($parentData instanceof kConvartableJobData))
				{
					KalturaLog::err("Parent job data is invalid");
					return null;
				}
				
				$operatorSet = new kOperatorSets();
				$operatorSet->setSerialized(/*stripslashes*/($flavor->getOperators()));
				$nextOperatorSet = $parentData->getCurrentOperationSet();
				$nextOperatorIndex = $parentData->getCurrentOperationIndex() + 1;
				$nextOperator = $operatorSet->getOperator($nextOperatorSet, $nextOperatorIndex);
				if(!$nextOperator)
				{
					KalturaLog::err("Next operator is invalid");
					return null;
				}
				
				KalturaLog::log("Moving to next operator [$nextOperatorIndex] in set [$nextOperatorSet]");
				$convertData->setCurrentOperationSet($nextOperatorSet);
				$convertData->setCurrentOperationIndex($nextOperatorIndex);
				
				$currentConversionEngine = $nextOperator->id;
			}
			else
			{
				// TODO remove after all old version flavors migrated
				
				KalturaLog::log("Last used conversion engine is [$lastEngineType]");
				// searching for $lastEngineType in the list
				while($lastEngineType != $currentConversionEngine && next($conversionEngines))
					$currentConversionEngine = current($conversionEngines);
					
				// takes the next engine
				$currentConversionEngine = next($conversionEngines);
				if(! $currentConversionEngine)
				{
					KalturaLog::err("There is no other conversion engine to use");
					return null;
				}
			}
		}
		KalturaLog::log("Using conversion engine [$currentConversionEngine]");
		
		self::contributeToConvertJobData($currentConversionEngine, $convertData);
		
		$dbCurrentConversionEngine = kPluginableEnumsManager::apiToCore('conversionEngineType', $currentConversionEngine);
		
		return $dbCurrentConversionEngine;
	}
	/**
	 * 
	 * Allow plugin to set additional information on ConvertJobData object
	 * 
	 * @param string $conversionEngineId
	 * @param kConvertJobData $convertData
	 */
	private static function contributeToConvertJobData($conversionEngineId, kConvertJobData &$convertData)
	{
		$plugin = kPluginableEnumsManager::getPlugin($conversionEngineId);
		if($plugin && $plugin instanceof IKalturaBatchJobDataContributor)
		{
			$convertData = $plugin->contributeToConvertJobData(BatchJobType::CONVERT, $conversionEngineId, $convertData);
		}
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $thumbAssetId
	 * @param FileSyncKey $srcSyncKey
	 * @param string $srcAssetId
	 * @param int $srcAssetType enum of assetType
	 * @param thumbParamsOutput $thumbParams
	 * @return BatchJob
	 */
	public static function addCapturaThumbJob(BatchJob $parentJob = null, $partnerId, $entryId, $thumbAssetId, FileSyncKey $srcSyncKey, $srcAssetId, $srcAssetType, thumbParamsOutput $thumbParams = null)
	{
		$thumbAsset = assetPeer::retrieveById($thumbAssetId);
		if(!$thumbAsset)
		{
			KalturaLog::err("No thumbnail asset found for id [$thumbAssetId]");
			return null;
		}
		
		$partner = PartnerPeer::retrieveByPK($thumbAsset->getPartnerId());
		
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		if(!$fileSync)
		{
			$thumbAsset->setStatus(asset::ASSET_STATUS_ERROR);
			$thumbAsset->setDescription("Source file sync not found: $srcSyncKey");
			$thumbAsset->save();
			
			KalturaLog::err("Source file sync not found: $srcSyncKey");
			return null;
		}
		
		if(!$local)
		{
			if(StorageProfile::shouldImportFile($fileSync, $partner))
			{
				$url = $fileSync->getExternalUrl($entryId);
				$originalAsset = kFileSyncUtils::retrieveObjectForSyncKey($srcSyncKey);
				if($originalAsset instanceof flavorAsset)
				{
					if($thumbParams)
					{
						$thumbParams->setSourceParamsId($originalAsset->getFlavorParamsId());
						$thumbParams->save();
					}
					
					$thumbAsset->setStatus(asset::ASSET_STATUS_WAIT_FOR_CONVERT);
					$thumbAsset->setDescription("Source file sync is importing: $srcSyncKey");
					$thumbAsset->save();
					
					return kJobsManager::addImportJob($parentJob, $thumbAsset->getEntryId(), $partner->getId(), $url, $originalAsset, null, null, true);
				}
				
				$downloadPath = myContentStorage::getFSUploadsPath() . '/' . $thumbAsset->getId() . '.jpg';
				if (KCurlWrapper::getDataFromFile($url, $downloadPath, null, true))
				{
					kFileSyncUtils::moveFromFile($downloadPath, $srcSyncKey);
					list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, false, false);
					if(!$fileSync)
						throw new kCoreException("Source file not found for thumbnail capture [$thumbAssetId]", kCoreException::SOURCE_FILE_NOT_FOUND);
				}
			}
			else
			{
				throw new kCoreException("Source file not found for thumbnail capture [$thumbAssetId]", kCoreException::SOURCE_FILE_NOT_FOUND);
			}
		}
		$remoteUrl = $fileSync->getExternalUrl($entryId);
		
		// creates convert data
		$data = new kCaptureThumbJobData();
		$data->setThumbAssetId($thumbAssetId);
		$data->setSrcAssetId($srcAssetId);
		$data->setSrcAssetEncryptionKey(self::getAssetEncyptionKey($srcAssetId));
		$data->setSrcAssetType($srcAssetType);
		$data->setFileContainer(self::getFileContainerByFileSync($fileSync));
		$data->setSrcFileSyncRemoteUrl($remoteUrl);
		$data->setThumbParamsOutputId($thumbParams->getId());
	
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::CAPTURE_THUMB, null, null, kDataCenterMgr::getCurrentDcId());
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		$batchJob->setObjectId($thumbAssetId);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		return kJobsManager::addJob($batchJob, $data, BatchJobType::CAPTURE_THUMB);
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param int $postConvertAssetType
	 * @param FileSyncKey $fileSyncKey
	 * @param int $flavorAssetId
	 * @param int $flavorParamsOutputId
	 * @param bool $createThumb
	 * @param int $thumbOffset
	 * @param string $customData
	 * @return BatchJob
	 */
	public static function addPostConvertJob(BatchJob $parentJob = null, $postConvertAssetType, $fileSyncKey, $flavorAssetId, $flavorParamsOutputId, $createThumb = false, $thumbOffset = 3)
	{
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($fileSyncKey);

		$postConvertData = new kPostConvertJobData();
		$postConvertData->setPostConvertAssetType($postConvertAssetType);
		$postConvertData->setSrcFileSyncLocalPath($fileSync);
		$postConvertData->setFlavorParamsOutputId($flavorParamsOutputId);
		$postConvertData->setFlavorAssetId($flavorAssetId);
		$postConvertData->setThumbOffset($thumbOffset);
		$postConvertData->setCreateThumb($createThumb);
		
		if($parentJob)
		{
			$parentData = $parentJob->getData();
			if($parentData instanceof kConvartableJobData)
			{
				$postConvertData->setCurrentOperationSet($parentData->getCurrentOperationSet());
				$postConvertData->setCurrentOperationIndex($parentData->getCurrentOperationIndex());
			}
		}
		
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		$flavorParamsOutput = assetParamsOutputPeer::retrieveByPK($flavorParamsOutputId);
		$unsupportedEncryptionFormats = kConf::get('unsupported_encryption_formats', 'runtime_config', array(assetParams::CONTAINER_FORMAT_MPEGTS));
		if($flavorAsset && $flavorAsset->getEncryptionKey() &&
			(!$flavorParamsOutput || ($flavorParamsOutput && !in_array($flavorParamsOutput->getFormat(), $unsupportedEncryptionFormats))))
		{
			$postConvertData->setFlavorAssetEncryptionKey($flavorAsset->getEncryptionKey());
		}
		
		if($createThumb)
		{
			if(!$flavorParamsOutput)
			{
				if($flavorAsset)
				{
					$postConvertData->setThumbHeight($flavorAsset->getHeight());
					$postConvertData->setThumbBitrate($flavorAsset->getBitrate());
				}
				else
				{
					$postConvertData->setCreateThumb(false);
				}
			}
			elseif(!$flavorParamsOutput->getVideoBitrate()) // audio only
			{
				$postConvertData->setCreateThumb(false);
			}
			elseif($flavorParamsOutput->getSourceRemoteStorageProfileId() != StorageProfile::STORAGE_KALTURA_DC)
			{
				$postConvertData->setCreateThumb(false);
			}
			elseif($flavorAsset)
			{
				$entry = $flavorAsset->getentry();
				if($entry)
				{
					$thisFlavorHeight = $flavorParamsOutput->getHeight();
					$thisFlavorBitrate = $flavorParamsOutput->getVideoBitrate();
					
					$createThumb = false;
					if($entry->getThumbBitrate() < $thisFlavorBitrate)
					{
						$createThumb = true;
					}
					elseif($entry->getThumbBitrate() == $thisFlavorBitrate && $entry->getThumbHeight() < $thisFlavorHeight)
					{
						$createThumb = true;
					}
					
					if($createThumb)
					{
						$postConvertData->setCreateThumb(true);
						$postConvertData->setThumbHeight($thisFlavorHeight);
						$postConvertData->setThumbBitrate($thisFlavorBitrate);
					}
				}
			}
		}
	
		$batchJob = null;
		$mediaParserType = ($flavorParamsOutput ? $flavorParamsOutput->getMediaParserType() : mediaParserType::MEDIAINFO);
		if($parentJob)
		{
			//Job will be created with parent job as his root job
			$useSameRoot = true;
			if($parentJob->getJobType() == BatchJobType::CONVERT_PROFILE)
				$useSameRoot = false;
				
			$batchJob = $parentJob->createChild(BatchJobType::POSTCONVERT, $mediaParserType, $useSameRoot); 
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($flavorAsset->getEntryId());
			$batchJob->setPartnerId($flavorAsset->getPartnerId());
		}
		
		$batchJob->setObjectId($flavorAsset->getId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		KalturaLog::log("Post Convert created with file: " . $postConvertData->getSrcFileSyncLocalPath());
		
		
		return kJobsManager::addJob($batchJob, $postConvertData, BatchJobType::POSTCONVERT, $mediaParserType);
	}
	
	public static function addImportJob(BatchJob $parentJob = null, $entryId, $partnerId, $entryUrl, asset $asset = null, $subType = null, kImportJobData $jobData = null, $keepCurrentVersion = false)
	{
		$entryUrl = str_replace('//', '/', $entryUrl);
		$entryUrl = preg_replace('/^((https?)|(ftp)|(scp)|(sftp)):\//', '$1://', $entryUrl);
		
		if (is_null($subType)) 
		{
    		if (stripos($entryUrl, 'sftp:') === 0) 
    		{
    		    $subType = kFileTransferMgrType::SFTP;
    		}
    		elseif (stripos($entryUrl, 'scp:') === 0) 
    		{
    		    $subType = kFileTransferMgrType::SCP;
    		}
    		elseif (stripos($entryUrl, 'ftp:') === 0) 
    		{
    		    $subType = kFileTransferMgrType::FTP;
    		}
    		elseif (stripos($entryUrl, 'https:') === 0) 
    		{
    		    $subType = kFileTransferMgrType::HTTPS;
    		}
    		else 
    		{
    		    $subType = kFileTransferMgrType::HTTP;
    		}
		}
		
		if (!$jobData) {
 		    $jobData = new kImportJobData();
		}
 		$jobData->setSrcFileUrl($entryUrl);
 		
 		if($asset)
 		{
 			if($keepCurrentVersion)
 			{
 				if(!$asset->isLocalReadyStatus())
	 				$asset->setStatus(asset::FLAVOR_ASSET_STATUS_IMPORTING);
 			}
 			else 
 			{
 				$asset->incrementVersion();
	 			$asset->setStatus(asset::FLAVOR_ASSET_STATUS_IMPORTING);
 			}
	 		$asset->save();
 			
 			$jobData->setFlavorAssetId($asset->getId());
 		}
 			
 		$entry = entryPeer::retrieveByPK($entryId);
		if($entry && !$asset instanceof CaptionAsset)
 		{
 			$higherStatuses = array(
 				entryStatus::PRECONVERT,
 				entryStatus::READY,
 			);
 			
 			if(!in_array($entry->getStatus(), $higherStatuses))
 			{
	 			$entry->setStatus(entryStatus::IMPORT);
	 			$entry->save();
 			}
 		}
 		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::IMPORT, $subType);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}

		$importToShared = kConf::get('enable_import_to_shared', 'runtime_config', null);
		$excludePartnersImportToShared = kConf::get('exclude_partners_import_to_shared', 'runtime_config', array());
		$sharedStorageProfileId = kDataCenterMgr::getSharedStorageProfileIds($partnerId, true);
		if($importToShared && $sharedStorageProfileId && !in_array($partnerId, $excludePartnersImportToShared))
		{
			$sharedStorageProfile = StorageProfilePeer::retrieveByPK($sharedStorageProfileId);
			$pathMgr = $sharedStorageProfile->getPathManager();
			
			$sharedPath = null;
			if($asset)
			{
				list($root, $path) = $pathMgr->generateFilePathArr($asset, asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $asset->getVersion());
			}
			elseif($entry)
			{
				$root = $sharedStorageProfile->getStorageBaseDir() . DIRECTORY_SEPARATOR . "entry/flavors/";
				$path = myContentStorage::getPathFromId($entry->getId()) . DIRECTORY_SEPARATOR . $entry->getId() . '_import';
			}
			else
			{
				//Not able to create any unique dir path based on asset or entry id so we will create random dir location
				$randomId = kString::generateStringId();
				$root = $sharedStorageProfile->getStorageBaseDir() . DIRECTORY_SEPARATOR . "entry/flavors/";
				$path = myContentStorage::getPathFromId($randomId) . DIRECTORY_SEPARATOR . $randomId . '_import';
			}
			
			$sharedPath = kFile::fixPath(rtrim($root, "/") . DIRECTORY_SEPARATOR . ltrim($path, "/"));
			$jobData->setDestFileSharedPath($sharedPath);
		}
		
		$batchJob->setObjectId($jobData->getFlavorAssetId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		return self::addJob($batchJob, $jobData, BatchJobType::IMPORT, $subType);
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param liveAsset $asset
	 * @param int $mediaServerIndex
	 * @param string $filePath
	 * @param float $endTime
	 * @param array $amfArray
	 */
	public static function addConvertLiveSegmentJob(BatchJob $parentJob = null, liveAsset $asset, $mediaServerIndex, $filePath, $endTime)
	{
		$keyType = liveAsset::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY;
		if($mediaServerIndex == EntryServerNodeType::LIVE_BACKUP)
			$keyType = liveAsset::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY;
			
		$key = $asset->getSyncKey($keyType);
		$files = array();
		if(kFileSyncUtils::fileSync_exists($key))
		{
			$files = kFileSyncUtils::dir_get_files($key, false);
		}
		
		$jobData = new kConvertLiveSegmentJobData();
 		$jobData->setEntryId($asset->getEntryId());
 		$jobData->setAssetId($asset->getId());
		$jobData->setMediaServerIndex($mediaServerIndex);
		$jobData->setEndTime($endTime);
		$jobData->setSrcFilePath($filePath);
		$jobData->setFileIndex(count($files));
 			
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::CONVERT_LIVE_SEGMENT);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($asset->getEntryId());
			$batchJob->setPartnerId($asset->getPartnerId());
		}
		
		$batchJob->setObjectId($asset->getEntryId());
		$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		return self::addJob($batchJob, $jobData, BatchJobType::CONVERT_LIVE_SEGMENT);
	}

	/**
	 * @param BatchJob $parentJob
	 * @param flavorAsset $asset
	 * @param array $files
	 * @param bool $shouldSort
	 * @param null $offset
	 * @param null $duration
	 * @param array $conversionCommands
	 * @return BatchJob
	 */
	public static function addConcatJob(BatchJob $parentJob = null, flavorAsset $asset, array $files, $shouldSort = true, $offset = null, $duration = null, $conversionCommands = array())
	{
		$jobData = new kConcatJobData();
 		$jobData->setSrcFiles($files);
		$jobData->setFlavorAssetId($asset->getId());
		$jobData->setOffset($offset);
		$jobData->setDuration($duration);
		$jobData->setShouldSort($shouldSort);
		$jobData->setConversionCommands($conversionCommands);

		$isMultiSource = $parentJob->getJobType() == KalturaBatchJobType::MULTI_CLIP_CONCAT;
		$jobData->setMultiSource($isMultiSource);

		$entry = $asset->getentry();
 		if($entry && $entry->getStatus() != entryStatus::READY)
		{
			$entry->setStatus(entryStatus::PRECONVERT);
			$entry->save();
		}

		$sharedStorageProfileId = kDataCenterMgr::getSharedStorageProfileIds($asset->getPartnerId(), true);
		if($sharedStorageProfileId)
		{
			$sharedStorageProfile = StorageProfilePeer::retrieveByPK($sharedStorageProfileId);
			$pathMgr = $sharedStorageProfile->getPathManager();
			
			//When convert is done we call incrementVersion so when creating the path we need to make sure path version is correct
			$nextVersion = kFileSyncUtils::calcObjectNewVersion($asset->getId(), $asset->getVersion(), FileSyncObjectType::ASSET, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			list($root, $path) = $pathMgr->generateFilePathArr($asset, asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $nextVersion);
			$sharedPath = kFile::fixPath(rtrim($root, "/") . DIRECTORY_SEPARATOR . ltrim($path, "/"));
		 
			$jobData->setDestFilePath($sharedPath);
		}
 	
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::CONCAT);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setPartnerId($asset->getPartnerId());
		}
		
		$batchJob->setEntryId($asset->getEntryId());
		$batchJob->setObjectId($jobData->getFlavorAssetId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		return self::addJob($batchJob, $jobData, BatchJobType::CONCAT);
	}
	
	/**
	 * @param int $partnerId
	 * @param int $objectType of enum IndexObjectType
	 * @param baseObjectFilter $filter The filter should return the list of objects that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the object columns and attributes values should be recalculated before reindexed
	 * @param array $featureStatusesToRemove - kFeatureStatus to remove when job is finished.
	 * @return BatchJob
	 */
	public static function addIndexJob($partnerId, $objectType, baseObjectFilter $filter, $shouldUpdate, $featureStatusesToRemove = array())
	{
	    $jobData = new kIndexJobData();
 		$jobData->setFilter($filter);
 		$jobData->setShouldUpdate($shouldUpdate);
 		$jobData->setFeatureStatusesToRemove($featureStatusesToRemove);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		
		return self::addJob($batchJob, $jobData, BatchJobType::INDEX, $objectType);
	}
	
	/**
	 * @param int $partnerId
	 * @param int $objectType of enum CopyObjectType
	 * @param baseObjectFilter $filter The filter should return the list of objects that need to be copied.
	 * @param BaseObject $templateObject Template object to overwrite attributes on the copied object
	 * @return BatchJob
	 */
	public static function addCopyJob($partnerId, $objectType, baseObjectFilter $filter, BaseObject $templateObject)
	{
	    $jobData = new kCopyJobData();
 		$jobData->setFilter($filter);
 		$jobData->setTemplateObject($templateObject);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		
		return self::addJob($batchJob, $jobData, BatchJobType::COPY, $objectType);
	}
	
	/**
	 * @param int $partnerId
	 * @param string $protocol http or https
	 * @param SessionType $ksType
	 * @param array $userRoles
	 * @param string $objectType class name
	 * @param string $objectId
	 * @param string $startObjectKey
	 * @param string $endObjectKey
	 */
	public static function addRecalculateResponseProfileCacheJob($partnerId, $protocol, $ksType, array $userRoles, $objectType, $objectId = null, $startObjectKey = null, $endObjectKey = null)
	{
	    $jobData = new kRecalculateResponseProfileCacheJobData();
 		$jobData->setProtocol($protocol);
 		$jobData->setKsType($ksType);
 		$jobData->setUserRoles($userRoles);
 		$jobData->setObjectType($objectType);
 		$jobData->setObjectId($objectId);
 		$jobData->setStartObjectKey($startObjectKey);
 		$jobData->setEndObjectKey($endObjectKey);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);

		if(is_subclass_of($objectType, 'entry'))
		{
			$batchJob->setObjectId($objectId);
			$batchJob->setEntryId($objectId);
			$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		}
		elseif(is_subclass_of($objectType, 'category'))
		{
			$batchJob->setObjectId($objectId);
			$batchJob->setObjectType(BatchJobObjectType::CATEGORY);
		}
		else
		{
			KalturaLog::warning("Object type [$objectType] is not expected to need cache recalculation");
			return null;
		}

		return self::addJob($batchJob, $jobData, BatchJobType::RECALCULATE_CACHE, RecalculateCacheType::RESPONSE_PROFILE);
	}
	
	/**
	 * @param int $partnerId
	 * @param int $objectType of enum DeleteObjectType
	 * @param baseObjectFilter $filter The filter should return the list of objects that need to be deleted
	 * @return BatchJob
	 */
	public static function addDeleteJob($partnerId, $objectType, baseObjectFilter $filter)
	{
	    $jobData = new kDeleteJobData();
 		$jobData->setFilter($filter);	
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		
		return self::addJob($batchJob, $jobData, BatchJobType::DELETE, $objectType);
	}
	
	public static function addBulkDownloadJob($partnerId, $puserId, $entryIds, $flavorParamsId)
	{
		$entryIds = explode(",", $entryIds);
		
		if (count($entryIds) > self::BULK_DOWNLOAD_TOTAL_ENTRIES_AMOUNT_RESTRICTION)
			throw new APIException(APIErrors::ENTRIES_AMOUNT_EXCEEDED);
		
		foreach($entryIds as $entryId)
		{
			$dbEntry = entryPeer::retrieveByPK($entryId);
			if (!$dbEntry)
				throw new APIException(APIErrors::INVALID_ENTRY_ID, $entryId);
		}
		
		$chunksOfEntries = array_chunk($entryIds, self::BULK_DOWLOAD_SINGLE_JOB_ENTRIES_AMOUNT);	
		$jobs = array();
			
		foreach($chunksOfEntries as $chunk)
		{
			$jobDb = new BatchJob();
			$jobDb->setPartnerId($partnerId);
			$data = new kBulkDownloadJobData();
		
			$data->setEntryIds(implode(",", $chunk));
			$data->setFlavorParamsId((int)$flavorParamsId);
			$data->setPuserId($puserId);
		
			$jobs[] = self::addJob($jobDb, $data, BatchJobType::BULKDOWNLOAD);
        }
        return $jobs;
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param entry $entry
	 * @param string $flavorAssetId
	 * @param FileSync $fileSync
	 * @return BatchJob
	 */
	public static function addConvertProfileJob(BatchJob $parentJob = null, entry $entry, $flavorAssetId, $fileSync = null)
	{
		if (!self::shouldExeConvertJob($fileSync))
		{
			$entry->setStatus(entryStatus::ERROR_CONVERTING);
			$entry->save();
			myEntryUtils::addTrackEntryInfo($entry,"Source file for conversion is not supported");
			return null;
		}
		if($entry->getConversionQuality() == conversionProfile2::CONVERSION_PROFILE_NONE)
		{
			$entry->setStatus(entryStatus::PENDING);
			$entry->save();
			
			KalturaLog::notice('Entry should not be converted');
			return null;
		}

		$inputFileSyncLocalPath = $fileSync->getFullPath();
		if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
		{
			$inputFileSyncLocalPath = $fileSync->getRemotePath();
		}

		$importingSources = false;
		// if file size is 0, do not create conversion profile and set entry status as error converting
		if (!kFile::checkFileExists($inputFileSyncLocalPath) || kFile::fileSize($inputFileSyncLocalPath) == 0)
		{
			KalturaLog::info("Input file [$inputFileSyncLocalPath] does not exist");
			
			$partner = $entry->getPartner();
			
			$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entry->getId());
			
			// load the asset params to the instance pool
			$flavorIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfile->getId());
			assetParamsPeer::retrieveByPKs($flavorIds);
					
			$conversionRequired = false;
			$sourceFileRequiredStorages = array();
			$sourceIncludedInProfile = false;
			$flavorAsset = assetPeer::retrieveById($flavorAssetId);
			$flavors = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfile->getId());
			KalturaLog::info("Found flavors [" . count($flavors) . "] in conversion profile [" . $conversionProfile->getId() . "]");
			foreach($flavors as $flavor)
			{
				/* @var $flavor flavorParamsConversionProfile */
				
				if($flavor->getFlavorParamsId() == $flavorAsset->getFlavorParamsId())
				{
					KalturaLog::info("Flavor [" . $flavor->getFlavorParamsId() . "] is ingested source");
					$sourceIncludedInProfile = true;
					continue;
				}
			
				$flavorParams = assetParamsPeer::retrieveByPK($flavor->getFlavorParamsId());
				
				if($flavorParams instanceof liveParams || $flavor->getOrigin() == assetParamsOrigin::INGEST)
				{
					KalturaLog::info("Flavor [" . $flavor->getFlavorParamsId() . "] should be ingested");
					continue;
				}
			
				if($flavor->getOrigin() == assetParamsOrigin::CONVERT_WHEN_MISSING)
				{
					$siblingFlavorAsset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $flavor->getFlavorParamsId());
					if($siblingFlavorAsset)
					{
						KalturaLog::info("Flavor [" . $flavor->getFlavorParamsId() . "] already ingested");
						continue;
					}
				}
				
				$sourceFileRequiredStorages[] = $flavorParams->getSourceRemoteStorageProfileId();
				
				$conversionRequired = true;
				break;
			}
			
			if($conversionRequired)
			{
				foreach($sourceFileRequiredStorages as $storageId)
				{
					if($storageId == StorageProfile::STORAGE_KALTURA_DC)
					{
						$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
						list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key, true, false);
						if(StorageProfile::shouldImportFile($fileSync, $partner))
						{
							$url = $fileSync->getExternalUrl($entry->getId(), null, true);
							kJobsManager::addImportJob($parentJob, $entry->getId(), $partner->getId(), $url, $flavorAsset, null, null, true);
							$importingSources = true;
							continue;
						}
					}
					elseif($flavorAsset->getExternalUrl($storageId))
					{
						continue;
					}
					
					kBatchManager::updateEntry($entry->getId(), entryStatus::ERROR_CONVERTING);
					
					$flavorAsset = assetPeer::retrieveById($flavorAssetId);
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
					$flavorAsset->setDescription('Entry of size 0 should not be converted');
					$flavorAsset->save();
					
					KalturaLog::err('Entry of size 0 should not be converted');
					return null;
				}
			}
			else
			{
				if($flavorAsset->getStatus() == asset::FLAVOR_ASSET_STATUS_QUEUED)
				{
					if($sourceIncludedInProfile)
						$flavorAsset->setStatusLocalReady();
					else
					{
						$flavorAsset->setStatus(asset::FLAVOR_ASSET_STATUS_DELETED);
						$flavorAsset->setDeletedAt(time());
					}
						
					$flavorAsset->save();
					
					if($sourceIncludedInProfile)
					{
						kBusinessPostConvertDL::handleConvertFinished(null, $flavorAsset);
					}
				}
				return null;
			}
		}
		
		if($entry->getStatus() != entryStatus::READY)
		{
			$entry->setStatus(entryStatus::PRECONVERT);
		}
		
		$jobData = new kConvertProfileJobData();
		$jobData->setFlavorAssetId($flavorAssetId);
		$jobData->setInputFileSyncLocalPath($inputFileSyncLocalPath);
		$jobData->setExtractMedia(true);
		
		if($entry->getType() != entryType::MEDIA_CLIP)
		{
			$jobData->setExtractMedia(false);
			$entry->setCreateThumb(false);
		}
		$entry->save();
 		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::CONVERT_PROFILE);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entry->getId());
			$batchJob->setPartnerId($entry->getPartnerId());
			$batchJob->setUseNewRoot(true);
		}
		
		$batchJob->setObjectId($entry->getId());
		$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		if($importingSources)
			$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		return self::addJob($batchJob, $jobData, BatchJobType::CONVERT_PROFILE);
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param string $entryId
	 * @param int $partnerId
	 * @param StorageProfile $externalStorage
	 * @param FileSync $fileSync
	 * @param FileSync $srcFileSync
	 * @param bool $force
	 * 
	 * @return BatchJob
	 */
	public static function addStorageExportJob(BatchJob $parentJob = null, $entryId, $partnerId, StorageProfile $externalStorage, FileSync $fileSync, FileSync $srcFileSync, $force = false, $dc = null)
	{
		$netStorageExportData = kStorageExportJobData::getInstance($externalStorage->getProtocol());
		$netStorageExportData->setStorageExportJobData($externalStorage, $fileSync, $srcFileSync);
				
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild( BatchJobType::STORAGE_EXPORT, $externalStorage->getProtocol(), false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		$batchJob->setObjectId($fileSync->getId());
		$batchJob->setObjectType(BatchJobObjectType::FILE_SYNC);
		$batchJob->setJobSubType($externalStorage->getProtocol());

		if(in_array($srcFileSync->getDc(), array_merge(kStorageExporter::getPeriodicStorageIds(), kDataCenterMgr::getSharedStorageProfileIds($partnerId))))
		{
			$batchJob->setDc(kDataCenterMgr::getCurrentDcId());
		}
		else
		{
			$batchJob->setDc($dc);
		}

		KalturaLog::log("Creating Storage export job, with source file: " . $netStorageExportData->getSrcFileSyncLocalPath()); 
		return self::addJob($batchJob, $netStorageExportData, BatchJobType::STORAGE_EXPORT, $externalStorage->getProtocol());
	}
	
	/**
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param int $srcCategoryId the source category id
	 * @param int $destCategoryId the destination category id
	 * @param bool $moveFromChildren indicates that all entries from all child categories should be moved as well
	 * @param bool $copyOnly indicates that the entries shouldn't be deleted from the source entry
	 * @param $fallback
	 * @return BatchJob
	 */
	public static function addMoveCategoryEntriesJob(BatchJob $parentJob = null, $partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren = false, $fallback = null)
	{
		$moveCategoryEntriesData = new kMoveCategoryEntriesJobData();
	    $moveCategoryEntriesData->setSrcCategoryId($srcCategoryId);
	    $moveCategoryEntriesData->setDestCategoryId($destCategoryId);
	    $moveCategoryEntriesData->setMoveFromChildren($moveFromChildren);
	    $moveCategoryEntriesData->setDestCategoryFullIds($fallback);
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::MOVE_CATEGORY_ENTRIES, null, false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setPartnerId($partnerId);
		}
		
		return self::addJob($batchJob, $moveCategoryEntriesData, BatchJobType::MOVE_CATEGORY_ENTRIES);
	}
	
	/**
	 * Update privacy context on category entries
	 * 
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param int $categoryId
	 */
	public static function addSyncCategoryPrivacyContextJob(BatchJob $parentJob = null, $partnerId, $categoryId)
	{
		$syncPrivacyContextData = new kSyncCategoryPrivacyContextJobData();
	    $syncPrivacyContextData->setCategoryId($categoryId);
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::SYNC_CATEGORY_PRIVACY_CONTEXT, null, false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setPartnerId($partnerId);
		}
		
		return self::addJob($batchJob, $syncPrivacyContextData, BatchJobType::SYNC_CATEGORY_PRIVACY_CONTEXT);
		
	}
	
	public static function addStorageDeleteJob(BatchJob $parentJob = null, $entryId = null, StorageProfile $storage, FileSync $fileSync)
	{
		$netStorageDeleteData = kStorageDeleteJobData::getInstance($storage->getProtocol());
        $netStorageDeleteData->setJobData($storage, $fileSync);
        
		if ($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::STORAGE_DELETE, $storage->getProtocol(), false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($storage->getPartnerId());
		}
		
		$batchJob->setObjectId($fileSync->getId());
		$batchJob->setObjectType(BatchJobObjectType::FILE_SYNC);
		$batchJob->setJobSubType($storage->getProtocol());
		KalturaLog::log("Creating Net-Storage Delete job, with source file: " . $netStorageDeleteData->getSrcFileSyncLocalPath()); 
		return self::addJob($batchJob, $netStorageDeleteData, BatchJobType::STORAGE_DELETE, $storage->getProtocol());
	}
	
	public static function addDeleteFileJob(BatchJob $parentJob = null, $entryId = null, $partnerId, $syncKey, $localFileSyncPath, $dc)
	{
		$deleteFileData = new kDeleteFileJobData();
		$deleteFileData->setLocalFileSyncPath($localFileSyncPath);
		$deleteFileData->setSyncKey($syncKey);

		if ($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::DELETE_FILE, null, false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		if(kDataCenterMgr::isDcIdShared($dc))
		{
			$batchJob->setDc(kDataCenterMgr::getCurrentDcId());
		}
		else
		{
			$batchJob->setDc($dc);
		}
		
		KalturaLog::log("Creating File Delete job, from data center id: ". $dc ." with source file: " . $deleteFileData->getLocalFileSyncPath());
		return self::addJob($batchJob, $deleteFileData, BatchJobType::DELETE_FILE );
	}
	
	
	public static function addExtractMediaJob(BatchJob $parentJob, $inputFileSyncLocalPath, $flavorAssetId)
	{
		$profile = null;
		
		try
		{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($parentJob->getEntryId());
			KalturaLog::info("profile [" . $profile->getId() . "]");
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
		
		$shouldCalculateComplexity = false;
		$mediaInfoEngine = mediaParserType::MEDIAINFO;
		if($profile)
		{
			$mediaInfoEngine = $profile->getMediaParserType();
			$shouldCalculateComplexity = $profile->getCalculateComplexity();
		}
		
		$extractMediaData = new kExtractMediaJobData();
		$srcFileSyncDescriptor = new kSourceFileSyncDescriptor();
		$srcFileSyncDescriptor->setFileSyncLocalPath($inputFileSyncLocalPath);
		$srcFileSyncDescriptor->setFileEncryptionKey(self::getEncryptionKeyForAssetId($flavorAssetId));
		$extractMediaData->setSrcFileSyncs(array($srcFileSyncDescriptor));
		$extractMediaData->setFlavorAssetId($flavorAssetId);
		$extractMediaData->setCalculateComplexity($shouldCalculateComplexity);
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		$entry = $flavorAsset->getentry();
		
		$shouldDetectGOP = null;
		if($entry)
		{
			if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
			{
				$extractMediaData->setExtractId3Tags(true);
			}
			elseif(kBusinessPreConvertDL::shouldCheckStaticContentFlow($entry))
			{
				$profileLC = kBusinessPreConvertDL::retrieveConversionProfileByType($entry);
				$shouldDetectGOP = $profileLC ? $profileLC->getDetectGOP() : null;
			}
			elseif($entry->getSourceType() == EntrySourceType::LECTURE_CAPTURE)
			{
				$profileLC = conversionProfile2Peer::retrieveByPartnerIdAndSystemName($entry->getPartnerId(), kBusinessPreConvertDL::$conditionalMapBySourceType[EntrySourceType::LECTURE_CAPTURE], ConversionProfileType::MEDIA);
				$shouldDetectGOP = $profileLC ? $profileLC->getDetectGOP() : null;
			}
		}
		
		if($shouldDetectGOP === null)
			$shouldDetectGOP = $profile ? $profile->getDetectGOP() : 0;
		$extractMediaData->setDetectGOP($shouldDetectGOP);
		
		//Added to support the flow where extract media will push the source file directly to the shared storage
		$pendingFileSync = $flavorAsset->getSharedPendingFileSync();
		if($pendingFileSync)
		{
			$extractMediaData->setSrcFileSyncRemoteUrl($pendingFileSync->getFullPath());
		}
		
		$batchJob = $parentJob->createChild(BatchJobType::EXTRACT_MEDIA, $mediaInfoEngine, false);
		$batchJob->setObjectId($flavorAssetId);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		KalturaLog::log("Creating Extract Media job, with source file: " . $extractMediaData->getSrcFileSyncLocalPath()); 
		return self::addJob($batchJob, $extractMediaData, BatchJobType::EXTRACT_MEDIA, $mediaInfoEngine);
	}

	private static function getEncryptionKeyForAssetId($flavorAssetId)
	{
		$asset = assetPeer::retrieveById($flavorAssetId);
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey);
		if ($fileSync && $fileSync->isEncrypted())
			return $fileSync->getEncryptionKey();
		return null;
	}
	
	public static function addNotificationJob(BatchJob $parentJob = null, $entryId, $partnerId, $notificationType, $sendType, $puserId, $objectId, $notificationData)
	{
		$jobData = new kNotificationJobData();
		$jobData->setType($notificationType);
		$jobData->setSendType($sendType);
		$jobData->setUserId($puserId);
		$jobData->setObjectId($objectId);
		$jobData->setData($notificationData);
 		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::NOTIFICATION, $notificationType);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
			
		if($sendType == kNotificationJobData::NOTIFICATION_MGR_NO_SEND || $sendType == kNotificationJobData::NOTIFICATION_MGR_SEND_SYNCH)
			$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		return self::addJob($batchJob, $jobData, BatchJobType::NOTIFICATION, $notificationType);
	}
	
	
	/**
	 * @param BatchJob $batchJob
	 * @param $data
	 * @param int $type
	 * @param int $subType
	 * @return BatchJob
	 */
	public static function addJob(BatchJob $batchJob, kJobData $data, $type, $subType = null)
	{
		$batchJob->setJobType($type);
		$batchJob->setJobSubType($subType);
		$batchJob->setData($data);
		
		if(!$batchJob->getParentJobId() && $batchJob->getEntryId())
		{
			$currentJob = kBatchManager::getCurrentUpdatingJob();
			if($currentJob && $currentJob->getEntryId() == $batchJob->getEntryId())
			{
				$batchJob->setParentJobId($currentJob->getId());
				$batchJob->setBulkJobId($currentJob->getBulkJobId());
				$batchJob->setRootJobId($currentJob->getRootJobId());
			}
			else
			{
				$entry = entryPeer::retrieveByPKNoFilter($batchJob->getEntryId()); // some jobs could be on deleted entry
				if($entry)
				{	
					$batchJob->setRootJobId($entry->getBulkUploadId());
					$batchJob->setBulkJobId($entry->getBulkUploadId());		
				}
			}
		}
		
		$lockInfo = new kLockInfoData($batchJob);
		$lockInfo->setEstimatedEffort($data->calculateEstimatedEffort($batchJob));
		$lockInfo->setPriority($data->calculatePriority($batchJob));
		$lockInfo->setUrgency($data->calculateUrgency($batchJob));
		$batchJob->setLockInfo($lockInfo);
		
		if(is_null($batchJob->getStatus())) {
			$batchJob = self::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
		} else {
			$batchJob = self::updateBatchJob($batchJob, $batchJob->getStatus());
		}

		return $batchJob;		
	}
	
	/**
	 * Function adds bulk upload job to the queue
	 * @param Partner $partner
	 * @param kBulkUploadJobData $jobData
	 * @param string $bulkUploadType
	 * @throws APIException
	 * @return BatchJob
	 */
	public static function addBulkUploadJob(Partner $partner, kBulkUploadJobData $jobData, $bulkUploadType = null, $objectId = null, $objectType = null)
	{
		$job = new BatchJob();
		$job->setPartnerId($partner->getId());
		$job->setJobType(BatchJobType::BULKUPLOAD);
		$job->setJobSubType($bulkUploadType);
		if(!is_null($objectId) && !is_null($objectType))
		{
			$job->setObjectId($objectId);
			$job->setObjectType($objectType);
		}
		
		if(is_null($jobData))
		{
			throw new APIException(APIErrors::BULK_UPLOAD_BULK_UPLOAD_TYPE_NOT_VALID, $bulkUploadType);
		}
		
		$job->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		$job = kJobsManager::addJob($job, $jobData, BatchJobType::BULKUPLOAD, $bulkUploadType);
		
		if(!is_null($jobData->getFilePath()))
		{
			$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
			if (myUploadUtils::isFileTypeRestricted($jobData->getFilePath(), $jobData->getFileName()))
			{
				throw new APIException(APIErrors::INVALID_FILE_TYPE, $jobData->getFileName());
			}
	//		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($csvFileData["tmp_name"]));
			try{
				kFileSyncUtils::moveFromFile($jobData->getFilePath(), $syncKey, true);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e);
				throw new APIException(APIErrors::BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR);
			}
			
			$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			$jobData->setFilePath($filePath);
		}
		
		if (!$jobData->getBulkUploadObjectType())
		{
		    $jobData->setBulkUploadObjectType(BulkUploadObjectType::ENTRY);
		}
			
		if ($jobData->getBulkUploadObjectType() == BulkUploadObjectType::ENTRY && !$jobData->getObjectData()->getConversionProfileId())
		{
			$jobData->setConversionProfileId($partner->getDefaultConversionProfileId());
			$kmcVersion = $partner->getKmcVersion();
		    $check = null;
			if($kmcVersion < 2)
    		{
    			$check = ConversionProfilePeer::retrieveByPK($jobData->getConversionProfileId());
    		}
    		else
    		{
    			$check = conversionProfile2Peer::retrieveByPK($jobData->getConversionProfileId());
    		}
    		if(!$check)
    			throw new APIException(APIErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $jobData->getConversionProfileId());
    	}

		$job->setData($jobData);
		return kJobsManager::updateBatchJob($job, BatchJob::BATCHJOB_STATUS_PENDING);
	}

	/**
	 * Copy aspects of one partner into another
	 * 
	 * @param int $fromPartnerId
	 * @param int $toPartnerId
	 * @return BatchJob
	 */
	public static function addCopyPartnerJob( $fromPartnerId, $toPartnerId )
	{
	    $jobData = new kCopyPartnerJobData();
	    $jobData->setFromPartnerId( $fromPartnerId );
	    $jobData->setToPartnerId( $toPartnerId );

		$batchJob = new BatchJob();
		$batchJob->setPartnerId( $toPartnerId ); // Associate with the "to" PID
		
		return self::addJob( $batchJob, $jobData, BatchJobType::COPY_PARTNER );
	}
	
	public static function addExportLiveReportJob($reportType, KalturaLiveReportExportParams $params)
	{
		// Calculate time offset from server time to UTC
		$dateTimeZoneServer = new DateTimeZone(kConf::get('date_default_timezone'));
		$dateTimeZoneUTC = new DateTimeZone("UTC");
		$dateTimeUTC = new DateTime("now", $dateTimeZoneUTC);
		$timeOffsetSeconds = -1 * $dateTimeZoneServer->getOffset($dateTimeUTC);
		
		// Create job data
		$jobData = new kLiveReportExportJobData();
		$jobData->entryIds = $params->entryIds;
		$jobData->recipientEmail = $params->recpientEmail;
		$jobData->timeZoneOffset = $timeOffsetSeconds - ($params->timeZoneOffset * 60); // Convert minutes to seconds
		$jobData->timeReference = time();
		$jobData->applicationUrlTemplate = $params->applicationUrlTemplate;
		
		
		$job = new BatchJob();
		$job->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$job->setJobType(BatchJobType::LIVE_REPORT_EXPORT);
		$job->setJobSubType($reportType);
		$job->setData($jobData);
		
		return self::addJob( $job, $jobData, BatchJobType::LIVE_REPORT_EXPORT, $reportType);
	}

	public static function getFileContainer(FileSyncKey $syncKey)
	{

		$fileSync = kFileSyncUtils::getResolveLocalFileSyncForKey($syncKey);
		return self::getFileContainerByFileSync($fileSync);
	}

	public static function addExportReportJob(KalturaReportExportParams $params)
	{
		// Calculate time offset from server time to UTC
		$dateTimeZoneServer = new DateTimeZone(kConf::get('date_default_timezone'));
		$dateTimeZoneUTC = new DateTimeZone("UTC");
		$dateTimeUTC = new DateTime("now", $dateTimeZoneUTC);
		$timeOffsetSeconds = -1 * $dateTimeZoneServer->getOffset($dateTimeUTC);

		$jobData = new kReportExportJobData();
		$coreParams = $params->toObject();

		$jobData->setRecipientEmail($coreParams->getRecipientEmail());
		$jobData->setRecipientName($coreParams->getRecipientName());
		$jobData->setReportItems($coreParams->getReportItems());

		$offset = $timeOffsetSeconds - ($params->timeZoneOffset * 60);// Convert minutes to seconds
		$jobData->setTimeZoneOffset($offset);
		$jobData->setTimeReference(time());
		$jobData->setReportsGroup($coreParams->getReportsItemsGroup());
		$jobData->setBaseUrl($coreParams->getBaseUrl());

		$job = new BatchJob();
		$job->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$job->setJobType(BatchJobType::REPORT_EXPORT);
		$job->setData($jobData);

		return self::addJob($job, $jobData, BatchJobType::REPORT_EXPORT);
	}

	protected static function getAssetEncyptionKey($assetId)
	{
		if (!$assetId)
		{
			return null;
		}

		$flavorAsset = assetPeer::retrieveById($assetId);

		if(!$flavorAsset)
		{
			return null;
		}

		return $flavorAsset->getEncryptionKey();
	}

	protected static function getFileContainerByFileSync(FileSync $fileSync)
	{
		$fileContainer = new FileContainer();
		if ($fileSync)
		{
			$fileContainer->setFilePath($fileSync->getFullPath());
			$fileContainer->setEncryptionKey($fileSync->getEncryptionKey());
			$fileContainer->setFileSize($fileSync->getFileSize());
		}
		return $fileContainer;
	}

	/**
	 * @param FileSync $fileSync
	 * @return bool
	 */
	private static function shouldExeConvertJob($fileSync)
 	{
		if (!$fileSync)
		{
			KalturaLog::notice('No file-sync supplied for conversion');
			return false;
		}
 		if (self::shouldBlockFileConversion($fileSync))
 		{
 			KalturaLog::notice('Source of type text will not be converted - FileSyncId [' . $fileSync->getId() . ']');
 			return false;
 		}
		return true;
	}

	/**
	* @param FileSync $fileSync
	* @return bool
	*/
	private static function shouldBlockFileConversion($fileSync)
	{
		if($fileSync->isEncrypted())
			$filePath = $fileSync->createTempClear();
		else
			$filePath = $fileSync->getFullPath();
		$actualFileDescription = trim(kFile::getFileDescription($filePath));
		$blackList = kConf::get('file_descriptions_black_list');
		$shouldBlock = in_array($actualFileDescription,$blackList['fileDescriptions']);
		if($fileSync->isEncrypted())
			$fileSync->deleteTempClear();
		return $shouldBlock;
	}

	public static function addExportCsvJob(kExportCsvJobData $jobData, $partnerId, $exportObjectType)
	{
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		$shouldExportCsvToSharedStorage = kConf::get('should_export_csv_to_shared_storage','runtime_config', null);
		if ($shouldExportCsvToSharedStorage)
		{
			$sharedPath = kPathManager::getExportCsvSharedPath($partnerId, null, true);
			$jobData->setSharedOutputPath($sharedPath);
		}
		return self::addJob($batchJob, $jobData, BatchJobType::EXPORT_CSV, $exportObjectType);
	}

	public static function addMultiClipCopyCuePointsJob($destEntryID, $partnerId, $kClipDescriptionArray)
	{
		$jobData = new kMultiClipCopyCuePointsJobData();
		$jobData->setClipsDescriptionArray($kClipDescriptionArray);
		$jobData->setDestinationEntryId($destEntryID);
		$batchJob = new BatchJob();
		$batchJob->setEntryId($destEntryID);
		$batchJob->setPartnerId($partnerId);
		return kJobsManager::addJob($batchJob, $jobData, BatchJobType::COPY_CUE_POINTS, CopyCuePointJobType::MULTI_CLIP);
	}

	protected static function shouldUseSharedStorageForEngine($conversionEngine)
	{
		$supportedEngines = array(KalturaConversionEngineType::KALTURA_COM,
			KalturaConversionEngineType::ON2,
			KalturaConversionEngineType::FFMPEG,
			KalturaConversionEngineType::MENCODER,
			KalturaConversionEngineType::ENCODING_COM,
			KalturaConversionEngineType::EXPRESSION_ENCODER3,
			KalturaConversionEngineType::CHUNKED_FFMPEG,
			KalturaConversionEngineType::FFMPEG_VP8,
			KalturaConversionEngineType::FFMPEG_AUX);
		$SharedSupportedEngines = Kconf::get('sharedStorageConversionEngines', 'cloud_storage', $supportedEngines);

		if(in_array($conversionEngine, $SharedSupportedEngines))
		{
			return true;
		}
		return false;
	}
}

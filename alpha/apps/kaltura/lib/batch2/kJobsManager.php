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
		$dbBatchJobs = BatchJobPeer::retrieveByEntryId($entryId);
		
		foreach($dbBatchJobs as $dbBatchJob) {
			$dbBatchJob->setMessage("Aborted entry");
			self::abortDbBatchJob($dbBatchJob);
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
			return $dbBatchJob;
		}
		
		$lockObject = $dbBatchJob->getBatchJobLock();
		
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
			if($force) {
				$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::ABORTED);
				$dbBatchJob->save();
			} 
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
	
	public static function addMailJob(BatchJob $parentJob = null, $entryId, $partnerId, $mailType, $mailPriority, $fromEmail, $fromName, $toEmail, array $bodyParams = array(), array $subjectParams = array(), $toName = null, $toId = null, $camaignId = null, $templatePath = null)
	{
	  	$jobData = new kMailJobData();
		$jobData->setMailPriority($mailPriority);
	 	$jobData->setMailType($mailType);
	 	
	 	$jobData->setFromEmail($fromEmail);
	 	$jobData->setFromName($fromName);
	 	
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
				$srcFileSyncDescriptor->setFileSyncLocalPath($fileSync->getFullPath());
			$srcFileSyncDescriptor->setFileSyncRemoteUrl($fileSync->getExternalUrl($entry->getId()));
			$srcFileSyncDescriptor->setAssetId($fileSync->getObjectId());			
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
		KalturaLog::debug('Add convert job for ['.$flavorAssetId.']');
		
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		if(!$flavorAsset)
		{
			KalturaLog::err("No flavor asset found for id [$flavorAssetId]");
			return null;
		}
		$partner = PartnerPeer::retrieveByPK($flavorAsset->getPartnerId());
		$srcFileSyncs = array();
		$waitForImportComplete = false;
		
		foreach ($srcSyncKeys as $srcSyncKey) 
		{		
			$srcFileSyncDescriptor = new kSourceFileSyncDescriptor();
			$addImportJob = false;
				
			$fileSync = self::getFileSyncForKey($srcSyncKey, $flavor, $flavorAsset, $partner, $addImportJob);
			if(!$fileSync)
				return null;
				
			$srcFlavorAsset = assetPeer::retrieveById($srcSyncKey->getObjectId());
			if($addImportJob)
			{
				KalturaLog::debug("Creates import job for remote file sync");

				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT);
				$flavorAsset->setDescription("Source file sync is importing: $srcSyncKey");
				$flavorAsset->save();

				$url = $fileSync->getExternalUrl($flavorAsset->getEntryId());
				kJobsManager::addImportJob($parentJob, $flavorAsset->getEntryId(), $partner->getId(), $url, $srcFlavorAsset, null, null, true);
				$waitForImportComplete = true;			
			}
			else 
			{
				if($flavor->getSourceRemoteStorageProfileId() == StorageProfile::STORAGE_KALTURA_DC)
				{
					if($fileSync->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_URL)	
						$srcFileSyncDescriptor->setFileSyncLocalPath($fileSync->getFullPath());							
				}
				else
				{
					$srcFileSyncDescriptor->setFileSyncLocalPath($fileSync->getFilePath());
				}
				$srcFileSyncDescriptor->setFileSyncRemoteUrl($fileSync->getExternalUrl($flavorAsset->getEntryId()));
				$srcFileSyncDescriptor->setAssetId($srcSyncKey->getObjectId());
				$srcFileSyncDescriptor->setAssetParamsId($srcFlavorAsset->getFlavorParamsId());
				$srcFileSyncs[] = $srcFileSyncDescriptor;
			}
		}
		
		if($waitForImportComplete)
			return;
			
		// creates convert data
		$convertData = new kConvertJobData();
		$convertData->setSrcFileSyncs($srcFileSyncs);
		$convertData->setMediaInfoId($mediaInfoId);
		$convertData->setFlavorParamsOutputId($flavor->getId());
		$convertData->setFlavorAssetId($flavorAssetId);
		$convertData->setConversionProfileId($conversionProfileId);
		$convertData->setPriority($priority);
		
		$dbCurrentConversionEngine = self::getNextConversionEngine($flavor, $parentJob, $lastEngineType, $convertData);
		if(!$dbCurrentConversionEngine)
			return null;
		
		// creats a child convert job
		if($parentJob)
		{
			$dbConvertFlavorJob = $parentJob->createChild( BatchJobType::CONVERT, $dbCurrentConversionEngine, $sameRoot);
			KalturaLog::log("Created from parent job");
		}
		else
		{
			$dbConvertFlavorJob = new BatchJob();
			$dbConvertFlavorJob->setPartnerId($flavor->getPartnerId());
			$dbConvertFlavorJob->setJobType(BatchJobType::CONVERT);
			$dbConvertFlavorJob->setJobSubType($dbCurrentConversionEngine);
			KalturaLog::log("Created from flavor convert job");
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
		
		return kJobsManager::addJob($dbConvertFlavorJob, $convertData, BatchJobType::CONVERT, $dbCurrentConversionEngine);
	}
	
	private static function getFileSyncForKey(FileSyncKey $srcSyncKey, flavorParamsOutput $flavor, asset $flavorAsset, Partner $partner, &$addImportJob)
	{
		$addImportJob = false;
		$isLocal = ($flavor->getSourceRemoteStorageProfileId() == StorageProfile::STORAGE_KALTURA_DC);
		
		if($isLocal)
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		else 
			$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($srcSyncKey, $flavor->getSourceRemoteStorageProfileId());		
		
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
			if($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL && $partner && $partner->getImportRemoteSourceForConvert())
				$addImportJob = true;
			else	
				throw new kCoreException("Source file not found for flavor conversion [$flavorAsset->getId()]", kCoreException::SOURCE_FILE_NOT_FOUND);
		}
		
		return $fileSync;		
	}
	
	private static function getNextConversionEngine(flavorParamsOutput $flavor, BatchJob $parentJob = null, $lastEngineType, kConvertJobData $convertData)
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
		$dbCurrentConversionEngine = kPluginableEnumsManager::apiToCore('conversionEngineType', $currentConversionEngine);
		
		return $dbCurrentConversionEngine;
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
			if($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL && $partner && $partner->getImportRemoteSourceForConvert())
			{
				$url = $fileSync->getExternalUrl($entryId);
				$originalAsset = kFileSyncUtils::retrieveObjectForSyncKey($srcSyncKey);
				if($originalAsset instanceof flavorAsset)
				{
					KalturaLog::debug("Creates import job for remote file sync [$url]");
					
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
				
				KalturaLog::debug("Downloading remote file sync [$url]");
				$downloadPath = myContentStorage::getFSUploadsPath() . '/' . $thumbAsset->getId() . '.jpg';
				if (KCurlWrapper::getDataFromFile($url, $downloadPath))
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
		$localPath = $fileSync->getFullPath();
		$remoteUrl = $fileSync->getExternalUrl($entryId);
		
		// creates convert data
		$data = new kCaptureThumbJobData();
		$data->setThumbAssetId($thumbAssetId);
		$data->setSrcAssetId($srcAssetId);
		$data->setSrcAssetType($srcAssetType);
		$data->setSrcFileSyncLocalPath($localPath);
		$data->setSrcFileSyncRemoteUrl($remoteUrl);
		$data->setThumbParamsOutputId($thumbParams->getId());
	
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::CAPTURE_THUMB);
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
	 * @param string $srcFileSyncLocalPath
	 * @param int $flavorAssetId
	 * @param int $flavorParamsOutputId
	 * @param bool $createThumb
	 * @param int $thumbOffset
	 * @param string $customData
	 * @return BatchJob
	 */
	public static function addPostConvertJob(BatchJob $parentJob = null, $postConvertAssetType, $srcFileSyncLocalPath, $flavorAssetId, $flavorParamsOutputId, $createThumb = false, $thumbOffset = 3)
	{
		$postConvertData = new kPostConvertJobData();
		$postConvertData->setPostConvertAssetType($postConvertAssetType);
		$postConvertData->setSrcFileSyncLocalPath($srcFileSyncLocalPath);
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
		if($createThumb)
		{
			$flavorParamsOutput = assetParamsOutputPeer::retrieveByPK($flavorParamsOutputId);
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
 		if($entry)
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
		
		$batchJob->setObjectId($jobData->getFlavorAssetId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		return self::addJob($batchJob, $jobData, BatchJobType::IMPORT, $subType);
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
		foreach($entryIds as $entryId)
		{
			$dbEntry = entryPeer::retrieveByPK($entryId);
			if (!$dbEntry)
				throw new APIException(APIErrors::INVALID_ENTRY_ID, $entryId);
		}
		
		$jobDb = new BatchJob();
		$jobDb->setPartnerId($partnerId);
		$data = new kBulkDownloadJobData();
		
		$data->setEntryIds(implode(",", $entryIds));
		$data->setFlavorParamsId($flavorParamsId);
		$data->setPuserId($puserId);
		
		return self::addJob($jobDb, $data, BatchJobType::BULKDOWNLOAD);
	}
	
	/**
	 * @param BatchJob $batchJob
	 * @param entry $entry
	 * @param string $flavorAssetId
	 * @param string $inputFileSyncLocalPath
	 * @return BatchJob
	 */
	public static function addConvertProfileJob(BatchJob $parentJob = null, entry $entry, $flavorAssetId, $inputFileSyncLocalPath)
	{	
		KalturaLog::debug("Parent job [" . ($parentJob ? $parentJob->getId() : 'none') . "] entry [" . $entry->getId() . "] flavor asset [$flavorAssetId] input file [$inputFileSyncLocalPath]");
		if($entry->getConversionQuality() == conversionProfile2::CONVERSION_PROFILE_NONE)
		{
			$entry->setStatus(entryStatus::PENDING);
			$entry->save();
			
			KalturaLog::notice('Entry should not be converted');
			return null;
		}
		
		$importingSources = false;
		// if file size is 0, do not create conversion profile and set entry status as error converting
		if (!file_exists($inputFileSyncLocalPath) || kFile::fileSize($inputFileSyncLocalPath) == 0)
		{
			KalturaLog::debug("Input file [$inputFileSyncLocalPath] does not exist");
			
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
			KalturaLog::debug("Found flavors [" . count($flavors) . "] in conversion profile [" . $conversionProfile->getId() . "]");
			foreach($flavors as $flavor)
			{
				/* @var $flavor flavorParamsConversionProfile */
				
				if($flavor->getFlavorParamsId() == $flavorAsset->getFlavorParamsId())
				{
					KalturaLog::debug("Flavor [" . $flavor->getFlavorParamsId() . "] is ingested source");
					$sourceIncludedInProfile = true;
					continue;
				}
			
				if($flavor->getOrigin() == assetParamsOrigin::INGEST)
				{
					KalturaLog::debug("Flavor [" . $flavor->getFlavorParamsId() . "] should be ingested");
					continue;
				}
			
				if($flavor->getOrigin() == assetParamsOrigin::CONVERT_WHEN_MISSING)
				{
					$siblingFlavorAsset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $flavor->getFlavorParamsId());
					if($siblingFlavorAsset)
					{
						KalturaLog::debug("Flavor [" . $flavor->getFlavorParamsId() . "] already ingested");
						continue;
					}
				}
				
				$flavorParams = assetParamsPeer::retrieveByPK($flavor->getFlavorParamsId());
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
						list($syncFile, $local) = kFileSyncUtils::getReadyFileSyncForKey($key, true, false);
						if($syncFile && $syncFile->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL && $partner && $partner->getImportRemoteSourceForConvert())
						{
							KalturaLog::debug("Creates import job for remote file sync");
							$url = $syncFile->getExternalUrl($entry->getId());
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
	 * @param SyncFile $fileSync
	 * @param string $srcFileSyncLocalPath
	 * @param bool $force
	 * 
	 * @return BatchJob
	 */
	public static function addStorageExportJob(BatchJob $parentJob = null, $entryId, $partnerId, StorageProfile $externalStorage, FileSync $fileSync, $srcFileSyncLocalPath, $force = false, $dc = null)
	{
		KalturaLog::debug("entryId[$entryId], partnerId[$partnerId], externalStorage id[" . $externalStorage->getId() . "], fileSync id[" . $fileSync->getId() . "], srcFileSyncLocalPath[$srcFileSyncLocalPath]");
		
		$netStorageExportData = kStorageExportJobData::getInstance($externalStorage->getProtocol());
		$netStorageExportData->setStorageExportJobData($externalStorage, $fileSync, $srcFileSyncLocalPath);
				
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
		$batchJob->setDc($dc);
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
	 * @return BatchJob
	 */
	public static function addMoveCategoryEntriesJob(BatchJob $parentJob = null, $partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren = false, $copyOnly = false,
			$fallback = null)
	{
		$moveCategoryEntriesData = new kMoveCategoryEntriesJobData();
	    $moveCategoryEntriesData->setSrcCategoryId($srcCategoryId);
	    $moveCategoryEntriesData->setDestCategoryId($destCategoryId);
	    $moveCategoryEntriesData->setMoveFromChildren($moveFromChildren);
	    $moveCategoryEntriesData->setCopyOnly($copyOnly);
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
	
	public static function addFutureDeletionJob(BatchJob $parentJob = null, $entryId = null, Partner $partner, $syncKey, $localFileSyncPath, $dc)
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
			$batchJob->setPartnerId($partner->getId());
		}
		
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_RETRY);
		$batchJob->setCheckAgainTimeout(12*60*60);
		$batchJob->setDc($dc);
		
		KalturaLog::log("Creating File Delete job, from data center id: ". $deleteFileData->getDC() ." with source file: " . $deleteFileData->getLocalFileSyncPath()); 
		return self::addJob($batchJob, $deleteFileData, BatchJobType::DELETE_FILE );
	}
	
	
	public static function addExtractMediaJob(BatchJob $parentJob, $inputFileSyncLocalPath, $flavorAssetId)
	{
		$profile = null;
		try{
			$profile = myPartnerUtils::getConversionProfile2ForEntry($parentJob->getEntryId());
			KalturaLog::debug("profile [" . $profile->getId() . "]");
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
		
		$mediaInfoEngine = mediaParserType::MEDIAINFO;
		if($profile)
			$mediaInfoEngine = $profile->getMediaParserType();
		
		$extractMediaData = new kExtractMediaJobData();
		$srcFileSyncDescriptor = new kSourceFileSyncDescriptor();
		$srcFileSyncDescriptor->setFileSyncLocalPath($inputFileSyncLocalPath);
		$extractMediaData->setSrcFileSyncs(array($srcFileSyncDescriptor));
		$extractMediaData->setFlavorAssetId($flavorAssetId);
		
		$batchJob = $parentJob->createChild(BatchJobType::EXTRACT_MEDIA, $mediaInfoEngine, false);
		$batchJob->setObjectId($flavorAssetId);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		
		KalturaLog::log("Creating Extract Media job, with source file: " . $extractMediaData->getSrcFileSyncLocalPath()); 
		return self::addJob($batchJob, $extractMediaData, BatchJobType::EXTRACT_MEDIA, $mediaInfoEngine);
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
		KalturaLog::debug("adding BulkUpload job");
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
}
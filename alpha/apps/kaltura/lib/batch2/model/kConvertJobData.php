<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertJobData extends kConvartableJobData
{
	const CONVERSION_MILTI_COMMAND_LINE_SEPERATOR = ';;;';
	const CONVERSION_FAST_START_SIGN = 'FS';
	const MIGRATION_FLAVOR_PRIORITY = 10;

	/**
	 * @var string
	 */
	private $destFileSyncLocalPath;

	/**
	 * @var string
	 */
	private $destFileSyncRemoteUrl;

	/**
	 * @var string
	 */
	private $logFileSyncLocalPath;

	/**
	 * @var string
	 */
	private $logFileSyncRemoteUrl;

	/**
	 * @var string
	 */
	private $flavorAssetId;

	/**
	 * @var string
	 */
	private $remoteMediaId;
	
	/**
	 * @var string
	 */
	private $conversionProfileId;
	
	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var array<kDestFileSyncDescriptor>
	 */
	private $extraDestFileSyncs;	
	
	/**
	 * @var string
	 */
	private $engineMessage;
	
	/**
	 * @return the $destFileSyncLocalPath
	 */
	public function getDestFileSyncLocalPath()
	{
		return $this->destFileSyncLocalPath;
	}

	/**
	 * @return the $logFileSyncLocalPath
	 */
	public function getLogFileSyncLocalPath()
	{
		return $this->logFileSyncLocalPath;
	}

	/**
	 * @param $remoteMediaId the $remoteMediaId to set
	 */
	public function setRemoteMediaId($remoteMediaId)
	{
		$this->remoteMediaId = $remoteMediaId;
	}

	/**
	 * @return the $remoteMediaId
	 */
	public function getRemoteMediaId()
	{
		return $this->remoteMediaId;
	}

	/**
	 * @param $destFileSyncRemoteUrl the $destFileSyncRemoteUrl to set
	 */
	public function setDestFileSyncRemoteUrl($destFileSyncRemoteUrl)
	{
		$this->destFileSyncRemoteUrl = $destFileSyncRemoteUrl;
	}

	/**
	 * @param $logFileSyncRemoteUrl the $logFileSyncRemoteUrl to set
	 */
	public function setLogFileSyncRemoteUrl($logFileSyncRemoteUrl)
	{
		$this->logFileSyncRemoteUrl = $logFileSyncRemoteUrl;
	}

	/**
	 * @return the $destFileSyncRemoteUrl
	 */
	public function getDestFileSyncRemoteUrl()
	{
		return $this->destFileSyncRemoteUrl;
	}

	/**
	 * @return the $logFileSyncRemoteUrl
	 */
	public function getLogFileSyncRemoteUrl()
	{
		return $this->logFileSyncRemoteUrl;
	}


	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @param $destFileSyncLocalPath the $destFileSyncLocalPath to set
	 */
	public function setDestFileSyncLocalPath($destFileSyncLocalPath)
	{
		$this->destFileSyncLocalPath = $destFileSyncLocalPath;
	}

	/**
	 * @param $logFileSyncLocalPath the $logFileSyncLocalPath to set
	 */
	public function setLogFileSyncLocalPath($logFileSyncLocalPath)
	{
		$this->logFileSyncLocalPath = $logFileSyncLocalPath;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}
	
	/**
	 * @param $conversionProfileId the $conversionProfileId to set
	 */
	public function setConversionProfileId($conversionProfileId)
	{
		$this->conversionProfileId = $conversionProfileId;
	}
	
	/**
	 * @param int $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}
	
	/**
	 * @return the $conversionProfileId
	 */
	public function getConversionProfileId()
	{
		return $this->conversionProfileId;
	}
	
	function calculateUrgency(BatchJob $batchJob) {

		$flavorParamsId = $this->getFlavorParamsOutput()->getFlavorParamsId();
		$isBulkupload = ($batchJob->getBulkJobId() !== null);
		$readiness = null;
		
			/*
			 * Raise urgency/priority of copy jobs
			 * since copy jobs are very short
			 */
		$flavorParams = assetParamsPeer::retrieveByPK($flavorParamsId);
		if(isset($flavorParams) && $flavorParams->getVideoCodec()==flavorParams::VIDEO_CODEC_COPY){
			return BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD;
		}
		
		if($this->priority == 0)
			self::calculatePriority($batchJob);

		if($this->priority == self::MIGRATION_FLAVOR_PRIORITY) 
			return BatchJobUrgencyType::MIGRATION_URGENCY;

		// If you have no conversion profile, there is no point in this calculation
		if(is_null($this->conversionProfileId))
		{
			if ($flavorParamsId == -1 ) //intermediate source flow
				return ($isBulkupload? BatchJobUrgencyType::REQUIRED_BULK_UPLOAD : BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD);

			return BatchJobUrgencyType::DEFAULT_URGENCY;
		}

		if($batchJob->getObjectId() && $batchJob->getObjectType()) {
			$batchJobs = BatchJobPeer::retrieveByJobTypeAndObject($batchJob->getObjectId(), $batchJob->getObjectType(), 
				$batchJob->getJobType(), $batchJob->getJobSubType());
			
			if(count($batchJobs)) {
				return $batchJobs[0]->getLockInfo()->getUrgency() + 1;
			}
		}

		// a conversion job will be considered as required in one of the following cases:
		// 1. The flavor is required
		// 2. There are no required flavors and this is the flavor is optional with the minimal bitrate
		// 3. all flavors are set as READY_BEHAVIOR_NO_IMPACT.
		// 4. if conversion job is for replacing an existing entry
		
		$optionalFlavorParamsIds = array();
		$hasRequired = false;
		$allNoImpact = true;
		
		// Go over all flavors and decide on cases 1-3
		$fpcps = flavorParamsConversionProfilePeer::retrieveByConversionProfile($this->conversionProfileId);
		foreach($fpcps as $fpcp) {

			if($fpcp->getFlavorParamsId() == $flavorParamsId)	// Case 1
				$readiness = $fpcp->getReadyBehavior();
			if($fpcp->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED) // Case 2
				$hasRequired = true;
			else if ($fpcp->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
				$optionalFlavorParamsIds[] = $fpcp->getFlavorParamsId();

			if($fpcp->getReadyBehavior() != flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT) // Case 3
				$allNoImpact = false;
		}
		// Case 2
		if((!$hasRequired) && ($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)) {
			//retrieve minimal bitrate out of "optional" flavorParams
			$flvParamsMinBitrate = assetParamsPeer::retrieveMinimalBitrate($optionalFlavorParamsIds);
			if(!is_null($flvParamsMinBitrate) && $flvParamsMinBitrate->getId() == $flavorParamsId)
				$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;
		}
		
		// Case 3
		if($allNoImpact)
			$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;

		// Case 4
		$entry = $batchJob->getEntry();
		if ($entry)
		{
			$replacedEntryId = $entry->getReplacedEntryId();
			if ($replacedEntryId)
				$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;
		}

		// Decide on the urgency by the readiness and the upload method
		if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
			return ($isBulkupload? BatchJobUrgencyType::REQUIRED_BULK_UPLOAD : BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD);
		else if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
			return ($isBulkupload? BatchJobUrgencyType::OPTIONAL_BULK_UPLOAD : BatchJobUrgencyType::OPTIONAL_REGULAR_UPLOAD);
		else if ($flavorParamsId == -1)
			return ($isBulkupload? BatchJobUrgencyType::REQUIRED_BULK_UPLOAD : BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD);
		else
			return (BatchJobUrgencyType::DEFAULT_URGENCY);
	}
	
	function calculateEstimatedEffort(BatchJob $batchJob) {
		$clipDuration = $this->getFlavorParamsOutput()->getClipDuration();
		if ( isset($clipDuration) ) {
			return $clipDuration;
		}
		$mediaInfo = mediaInfoPeer::retrieveByPK($this->getMediaInfoId());
		if(is_null($mediaInfo)) {
			$sumEffort = 0;
			$fileSyncs = $this->getSrcFileSyncs();
			foreach ($fileSyncs as $fileSync) {
				$fileSize = filesize($fileSync->getFileSyncLocalPath());
				if($fileSize !== False)
					$sumEffort += $fileSize;
			}
				
			if($sumEffort != 0)
				return $sumEffort;
			return self::MAX_ESTIMATED_EFFORT;
			
		} else {
			return max($mediaInfo->getVideoDuration(),$mediaInfo->getAudioDuration(),$mediaInfo->getContainerDuration());
		}
	}
	
	public function calculatePriority(BatchJob $batchJob) {
		
		if($this->priority == 0) {
			$flavorParamsId = $this->getFlavorParamsOutput()->getFlavorParamsId();
			$fpcp = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavorParamsId, $this->conversionProfileId);
			if((!is_null($fpcp) && (($fpcp->getPriority() != 0)))) {
				$this->priority = $fpcp->getPriority();
			} else 
				$this->priority = parent::calculatePriority($batchJob);
		}
		return $this->priority;
	}
	
	/**
	 * @return the $destFileAssets
	 */
	public function getExtraDestFileSyncs() {
		return $this->extraDestFileSyncs;
	}
	
	/**
	 * @param array<kDestFileSyncDescriptor> $destFileSyncs
	 */
	public function setExtraDestFileSyncs($destFileSyncs) {
		$this->extraDestFileSyncs = $destFileSyncs;
	}
	
	/**
	 * @return the $engineMessage
	 */
	public function getEngineMessage() {
		return $this->engineMessage;
	}

	/**
	 * @param string $engineMessage
	 */
	public function setEngineMessage($engineMessage) {
		$this->engineMessage = $engineMessage;
	}
	
	
	
}

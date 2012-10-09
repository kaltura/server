<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertJobData extends kConvartableJobData
{
	const CONVERSION_MILTI_COMMAND_LINE_SEPERATOR = ';';
	const CONVERSION_FAST_START_SIGN = 'FS';


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
		
		$fpcps = flavorParamsConversionProfilePeer::retrieveByConversionProfile($this->conversionProfileId);
		
		// a conversion job will be considered as required in one of the following cases:
		// 1. The flavor is required
		// 2. There are no required flavors and this is the flavor is optional with the minimal bitrate
		// 3. all flavors are set as READY_BEHAVIOR_NO_IMPACT.
		
		$allFlavorParamsIds = array();
		$hasRequired = false;
		$allNoImpact = true;
		
		// Go over all flavors and decide on cases 1-3
		foreach($fpcps as $fpcp) {
			$allFlavorParamsIds[] = $fpcp->getFlavorParamsId();
			if($fpcp->getFlavorParamsId() == $flavorParamsId)	// Case 1
				$readiness = $fpcp->getReadyBehavior();
			if($fpcp->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED) // Case 2
				$hasRequired = true;
			if($fpcp->getReadyBehavior() != flavorParamsConversionProfile::READY_BEHAVIOR_NO_IMPACT) // Case 3
				$allNoImpact = false;
		}
			
		// Case 2
		if((!$hasRequired) && ($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)) {
			$flvParamsMinBitrate = assetParamsPeer::retrieveMinimalBitrate($allFlavorParamsIds);
			if($flvParamsMinBitrate->getId() == $flavorParamsId)
				$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;
		}
		
		// Case 3
		if($allNoImpact)
			$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;
			
		// Decide on the urgency by the readiness and the upload method
		if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
			return ($isBulkupload? BatchJobUrgencyType::REQUIRED_BULK_UPLOAD : BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD);
		else if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
			return ($isBulkupload? BatchJobUrgencyType::OPTIONAL_BULK_UPLOAD : BatchJobUrgencyType::OPTIONAL_REGULAR_UPLOAD);
		else
			return (BatchJobUrgencyType::DEFAULT_URGENCY);
	}
	
	function calculateEstimatedEffort(BatchJob $batchJob) {
		$mediaInfo = mediaInfoPeer::retrieveByPK($this->getMediaInfoId());
		if(is_null($mediaInfo)) {
			return self::MAX_ESTIMATED_EFFORT;
		} else {
			return max($mediaInfo->getVideoDuration(),$mediaInfo->getAudioDuration(),$mediaInfo->getContainerDuration());
		}
	}
	
}

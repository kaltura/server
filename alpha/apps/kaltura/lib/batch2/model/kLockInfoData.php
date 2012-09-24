<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLockInfoData 
{
	private $lockVersion;
	
	private $urgency;
	
	private $estimatedEffort;
	
	public function __construct(BatchJob $batchJob) {
		$this->lockVersion = 0;
		$this->urgency = BatchJobUrgencyType::getDefaultUrgency($batchJob);
	}
	
	/**
	 * @return the $lockVersion
	 */
	public function getLockVersion() {
		return $this->lockVersion;
	}

	/**
	 * @param field_type $lockVersion
	 */
	public function setLockVersion($lockVersion) {
		$this->lockVersion = $lockVersion;
	}

	/**
	 * @return the $urgency
	 */
	public function getUrgency() {
		return $this->urgency;
	}

	/**
	 * @return the $estimatedEffort
	 */
	public function getEstimatedEffort() {
		return $this->estimatedEffort;
	}

	/**
	 * @param field_type $urgency
	 */
	public function setUrgency($urgency) {
		$this->urgency = $urgency;
	}

	/**
	 * @param field_type $estimatedEffort
	 */
	public function setEstimatedEffort($estimatedEffort) {
		$this->estimatedEffort = $estimatedEffort;
	}
	
	/**
	 * @param flavor
	 * @param conversionProfileId
	 * @param dbConvertFlavorJob
	 */
	public function fillLockInfo($flavor, $conversionProfileId, $dbConvertFlavorJob, $estimateEffort) {
		
		$flavorParamsId = $flavor->getFlavorParamsId();
		$isBulkupload = ($dbConvertFlavorJob->getBulkJobId() !== null);
		$readiness = null;
	
		if($dbConvertFlavorJob !== null) {
			
			$fpcps = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfileId);

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
			
		} else {
			$readiness = flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED;
		}
	
		$this::setEstimatedEffort($estimateEffort);
		
		// Decide on the urgency by the readiness and the upload method
		if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
			$this::setUrgency($isBulkupload? BatchJobUrgencyType::REQUIRED_BULK_UPLOAD : BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD);
		else if($readiness == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
			$this::setUrgency($isBulkupload? BatchJobUrgencyType::OPTIONAL_BULK_UPLOAD : BatchJobUrgencyType::OPTIONAL_REGULAR_UPLOAD);
		else
			$this::setUrgency(BatchJobUrgencyType::DEFAULT_URGENCY);
	}
}

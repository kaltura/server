<?php


/**
 * Skeleton subclass for representing a row from the 'distribution_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
abstract class DistributionProfile extends BaseDistributionProfile 
{
	/**
	 * @return IDistributionProvider
	 */
	abstract public function getProvider();
	
	/**
	 * @return array<kDistributionThumbDimensions>
	 */
	public function getRequiredThumbDimensionsObjects()
	{
		$requiredThumbDimensionsStr = $this->getRequiredThumbDimensions();
		$requiredThumbDimensions = array();
		
		if($requiredThumbDimensionsStr)
			$requiredThumbDimensions = unserialize($requiredThumbDimensionsStr);
			
		if(!$requiredThumbDimensions)
			return array();
			
		return $requiredThumbDimensions;
	}
	
	/**
	 * @param array<kDistributionThumbDimensions> $v
	 * @return DistributionProfile The current object (for fluent API support)
	 */
	public function setRequiredThumbDimensionsObjects(array $v)
	{
		$requiredThumbDimensionsStr = serialize($v);
		return $this->setRequiredThumbDimensions($requiredThumbDimensionsStr);
	}
	
	/**
	 * @return array<kDistributionThumbDimensions>
	 */
	public function getOptionalThumbDimensionsObjects()
	{
		$optionalThumbDimensionsStr = $this->getOptionalThumbDimensions();
		$optionalThumbDimensions = array();
		
		if($optionalThumbDimensionsStr)
			$optionalThumbDimensions = unserialize($optionalThumbDimensionsStr);
			
		if(!$optionalThumbDimensions)
			return array();
			
		return $optionalThumbDimensions;
	}
	
	/**
	 * @param array<kDistributionThumbDimensions> $v
	 * @return DistributionProfile The current object (for fluent API support)
	 */
	public function setOptionalThumbDimensionsObjects(array $v)
	{
		$OptionalThumbDimensionsStr = serialize($v);
		return $this->setOptionalThumbDimensions($OptionalThumbDimensionsStr);
	}
	
	/**
	 * @return array<kDistributionThumbDimensions>
	 */
	public function getThumbDimensionsObjects()
	{
		return array_merge($this->getRequiredThumbDimensionsObjects(), $this->getOptionalThumbDimensionsObjects());
	}
	
	/**
	 * @see content_distribution/lib/model/om/BaseDistributionProfile#getRequiredFlavorParamsIds()
	 * @return array
	 */
	public function getRequiredFlavorParamsIdsArray()
	{
		if(!$this->getRequiredFlavorParamsIds())
			return array();
			
		return explode(',', $this->getRequiredFlavorParamsIds());
	}

	public function getAutoCreateFlavorsArray()
	{
		if(!$this->getAutoCreateFlavors())
			return array();
			
		return explode(',', $this->getAutoCreateFlavors());
	}

	public function getAutoCreateThumbArray()
	{
		if(!$this->getAutoCreateThumb())
			return array();
			
		return explode(',', $this->getAutoCreateThumb());
	}
			
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param int $action enum from DistributionAction
	 * @return array<kDistributionValidationError>
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = array();
		
		$requiredFlavorParamsIds = $this->getRequiredFlavorParamsIdsArray();
		$entryFlavorAssets = flavorAssetPeer::retreiveReadyByEntryId($entryDistribution->getEntryId());
		
		$requiredFlavorParamsIdsKeys = array_flip($requiredFlavorParamsIds);
		foreach($entryFlavorAssets as $entryFlavorAsset)
		{
			$flavorParamsId = $entryFlavorAsset->getFlavorParamsId();
			if(isset($requiredFlavorParamsIdsKeys[$flavorParamsId]))
				unset($requiredFlavorParamsIds[$requiredFlavorParamsIdsKeys[$flavorParamsId]]);
		}
		
		foreach($requiredFlavorParamsIds as $requiredFlavorParamsId)
		{
			$validationError = new kDistributionValidationError();
			$validationError->setAction($action);
			$validationError->setErrorType(DistributionErrorType::MISSING_FLAVOR);
			$validationError->setData($requiredFlavorParamsId);
			
			$validationErrors[] = $validationError;
		}
		
		$requiredThumbDimensions = $this->getRequiredThumbDimensionsObjects();
		$entryThumbAssets = thumbAssetPeer::retreiveReadyByEntryId($entryDistribution->getEntryId());
		
		$requiredThumbDimensionsWithKeys = array();
		foreach($requiredThumbDimensions as $requiredThumbDimension)
		{
			$key = $requiredThumbDimension->getKey();
			$requiredThumbDimensionsWithKeys[$key] = $requiredThumbDimension;
		}
		
		foreach($entryThumbAssets as $entryThumbAsset)
		{
			$key = $entryThumbAsset->getWidth() . 'x' . $entryThumbAsset->getHeight();
			if(isset($requiredThumbDimensionsWithKeys[$key]))
				unset($requiredThumbDimensionsWithKeys[$key]);
		}
		
		foreach($requiredThumbDimensionsWithKeys as $key => $requiredThumbDimension)
		{
			$validationError = new kDistributionValidationError();
			$validationError->setAction($action);
			$validationError->setErrorType(DistributionErrorType::MISSING_THUMBNAIL);
			$validationError->setData($key);
			
			$validationErrors[] = $validationError;
		}
				
		return $validationErrors;
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(DistributionProfilePeer::STATUS) && $this->getStatus() == DistributionProfileStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
} // DistributionProfile

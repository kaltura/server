<?php


/**
 * Skeleton subclass for representing a row from the 'generic_distribution_provider' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class GenericDistributionProvider extends BaseGenericDistributionProvider implements IDistributionProvider 
{
	const CUSTOM_DATA_FIELD_SCHEDULE_UPDATE_ENABLED = "scheduleUpdateEnabled";
	const CUSTOM_DATA_FIELD_DELETE_INSTEAD_UPDATE = "deleteInsteadUpdate";
	const CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNRISE = "intervalBeforeSunrise";
	const CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNSET = "intervalBeforeSunset";
	const CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS = "updateRequiredEntryFields";
	const CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS = "updateRequiredMetadataXPaths";
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return DistributionProviderType::GENERIC;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($this->getId(), DistributionAction::DELETE);
		return !is_null($action);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($this->getId(), DistributionAction::UPDATE);
		return !is_null($action);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($this->getId(), DistributionAction::FETCH_REPORT);
		return !is_null($action);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SCHEDULE_UPDATE_ENABLED);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DELETE_INSTEAD_UPDATE);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNRISE);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNSET);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS);
	}
	
	/* (non-PHPdoc)
	 * @see BaseGenericDistributionProvider::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(GenericDistributionProviderPeer::STATUS) && $this->getStatus() == GenericDistributionProviderStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	/**
	 * @return array<kDistributionThumbDimensions>
	 */
	public function getRequiredThumbDimensionsObjects()
	{
		$requiredThumbDimensionsStr = $this->getRequiredThumbDimensions();
		$requiredThumbDimensions = array();
		
		if($requiredThumbDimensionsStr)
		{
			try{
				$requiredThumbDimensions = unserialize($requiredThumbDimensionsStr);
			}
			catch(Exception $e){
				KalturaLog::err("Unable to unserialize [$requiredThumbDimensionsStr]");
			}
		}
			
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
		{
			try{
				$optionalThumbDimensions = unserialize($optionalThumbDimensionsStr);
			}
			catch(Exception $e){
				KalturaLog::err("Unable to unserialize [$optionalThumbDimensionsStr]");
			}
		}
			
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
	
	public function setScheduleUpdateEnabled($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SCHEDULE_UPDATE_ENABLED, $v);}
	public function setDeleteInsteadUpdate($v)			{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_DELETE_INSTEAD_UPDATE, $v);}
	public function setIntervalBeforeSunrise($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNRISE, $v);}
	public function setIntervalBeforeSunset($v)			{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_INTERVAL_BEFORE_SUNSET, $v);}
	public function setUpdateRequiredEntryFields($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS, $v);}
	public function setUpdateRequiredMetadataXpaths($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS, $v);}
	
} // GenericDistributionProvider

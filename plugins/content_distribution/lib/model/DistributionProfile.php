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
 * @package plugins.contentDistribution
 * @subpackage model
 */
abstract class DistributionProfile extends BaseDistributionProfile implements ISyncableFile, IRelatedObject
{
	const FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG = 1;
	
	const CUSTOM_DATA_FIELD_CONFIG_VERSION							= "configVersion";
	const CUSTOM_DATA_FIELD_SUNRISE_DEFAULT_OFFSET					= "sunriseDefaultOffset";
	const CUSTOM_DATA_FIELD_SUNSET_DEFAULT_OFFSET					= "sunsetDefaultOffset";
	const CUSTOM_DATA_FIELD_RECOMMENDED_STORAGE_PROFILE_DOWNLOAD	= "recommendedStorageProfileForDownload";
	const CUSTOM_DATA_FIELD_RECOMMENDED_DC_DOWNLOAD					= "recommendedDcForDownload";
	const CUSTOM_DATA_FIELD_RECOMMENDED_DC_EXECUTE					= "recommendedDcForExecute";
	const CUSTOM_DATA_FIELD_REQUIRED_ASSET_DISTRIBUTION_RULES		= "requiredAssetDistributionRules";
	const CUSTOM_DATA_FIELD_OPTIONAL_ASSET_DISTRIBUTION_RULES		= "optionalAssetDistributionRules";
	const CUSTOM_DATA_FIELD_DISTRIBUTE_TRIGGER						= "distributeTrigger";
	
	/**
	 * @return IDistributionProvider
	 */
	abstract public function getProvider();
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG:
				return $this->getConfigVersion();
		}
		return null;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(ContentDistributionFileSyncObjectType::DISTRIBUTION_PROFILE, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = ContentDistributionPlugin::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::DISTRIBUTION_PROFILE);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/distribution/profile/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
	
		$extension = 'conf';
		
		return $this->getId() . "_{$sub_type}_{$version}.{$extension}";	
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->m_file_sync; 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $file_sync)
	{
		 $this->m_file_sync = $file_sync;
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
		$existsKeys = array();
		foreach($v as $index => $dimension)
		{
			$key = $dimension->getKey();
			if(in_array($key, $existsKeys))
				unset($v[$index]);
			else
				$existsKeys[] = $key;
		}
		
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
		$existsKeys = array();
		foreach($v as $index => $dimension)
		{
			$key = $dimension->getKey();
			if(in_array($key, $existsKeys))
				unset($v[$index]);
			else
				$existsKeys[] = $key;
		}
		
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
		if(is_null($this->getRequiredFlavorParamsIds()) || !strlen($this->getRequiredFlavorParamsIds()))
			return array();
			
		return explode(',', $this->getRequiredFlavorParamsIds());
	}
	
	/**
	 * @see content_distribution/lib/model/om/BaseDistributionProfile#getOptionalFlavorParamsIds()
	 * @return array
	 */
	public function getOptionalFlavorParamsIdsArray()
	{
		if(is_null($this->getOptionalFlavorParamsIds()) || !strlen($this->getOptionalFlavorParamsIds()))
			return array();
			
		return explode(',', $this->getOptionalFlavorParamsIds());
	}
	
	private function filterEmptyString($value)
	{
		return strlen(strval($value));
	}
	
	/**
	 * @see content_distribution/lib/model/om/BaseDistributionProfile#setRequiredFlavorParamsIdsArray()
	 */
	public function setRequiredFlavorParamsIdsArray(array $v)
	{
		$v = array_filter($v, array($this, filterEmptyString));
		return $this->setRequiredFlavorParamsIds(implode(',', array_unique($v)));
	}

	public function getAutoCreateFlavorsArray()
	{
		if(is_null($this->getAutoCreateFlavors()) || !strlen($this->getAutoCreateFlavors()))
			return array();
			
		return explode(',', $this->getAutoCreateFlavors());
	}

	public function getAutoCreateThumbArray()
	{
		if(is_null($this->getAutoCreateThumb()) || !strlen($this->getAutoCreateThumb()))
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
		
		$distributionProvider = $this->getProvider();
		if(!$distributionProvider)
		{
			KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $this->getProviderType() . "] not found");
			return $validationErrors;
		}
		
		if($action == DistributionAction::UPDATE || $entryDistribution->getStatus() == EntryDistributionStatus::READY || $entryDistribution->getStatus() == EntryDistributionStatus::ERROR_UPDATING)
		{
			if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				return $validationErrors;
			}
		}
		
		$requiredFlavorParamsIds = $this->getRequiredFlavorParamsIdsArray();
		KalturaLog::log("Required Flavor Params Ids [" . print_r($requiredFlavorParamsIds, true) . "]");
		$entryFlavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($entryDistribution->getEntryId());
		
		$requiredFlavorParamsIdsKeys = array_flip($requiredFlavorParamsIds);
		foreach($entryFlavorAssets as $entryFlavorAsset)
		{
			$flavorParamsId = $entryFlavorAsset->getFlavorParamsId();
			if(isset($requiredFlavorParamsIdsKeys[$flavorParamsId]))
				unset($requiredFlavorParamsIds[$requiredFlavorParamsIdsKeys[$flavorParamsId]]);
		}
		
		foreach($requiredFlavorParamsIds as $requiredFlavorParamsId)
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_FLAVOR, $requiredFlavorParamsId);
		
		$requiredThumbDimensions = $this->getRequiredThumbDimensionsObjects();
		KalturaLog::log("Required Thumb Dimensions [" . print_r($requiredThumbDimensions, true) . "]");
		$entryThumbAssets = assetPeer::retrieveReadyThumbnailsByEntryId($entryDistribution->getEntryId());
		
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
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_THUMBNAIL, $key);
		
		$entryAssets = assetPeer::retrieveReadyByEntryId($entryDistribution->getEntryId());
		
		$requiredAssetDistributionRules = $this->getRequiredAssetDistributionRules();
		foreach($requiredAssetDistributionRules as $entryAssetDistributionRule)
		{
			$foundMatchingAsset = false;
			
			/* @var $entryAssetDistributionRule kAssetDistributionRule */
			foreach($entryAssets as $entryAsset)
			{
				/* @var $entryAsset asset */
				if ($entryAssetDistributionRule->fulfilled($entryAsset))
				{
					$foundMatchingAsset = true;
					break;
				}
			}
			
			if (!$foundMatchingAsset)
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_ASSET, $entryAssetDistributionRule->getValidationError());
			}
		}
				
		return $validationErrors;
	}


	/**
	 * @param array<Metadata> $metadataObjects
	 * @param string $field
	 * @return array|string
	 */
	public function findMetadataValue(array $metadataObjects, $field)
	{
		$results = array();
		foreach($metadataObjects as $metadata)
		{
			$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			$xmlContent = kFileSyncUtils::file_get_contents($key, true, false);
			
			$xml = new DOMDocument();
			$xml->loadXML($xmlContent);
			
			$nodes = $xml->getElementsByTagName($field);
			foreach($nodes as $node)
				$results[] = $node->textContent;
		}
		
		return $results;
	}

	public function createCustomValidationError($action, $type, $fieldName, $errorMsg)
	{
		$validationError = $this->createValidationError($action, $type, $fieldName);
		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
		$validationError->setValidationErrorParam($errorMsg);
		return $validationError;
	}


	public function createValidationError($action, $type, $data = null, $description = null)
	{
		$validationError = new kDistributionValidationError();
		$validationError->setAction($action);
		$validationError->setErrorType($type);
		$validationError->setData($data);
		$validationError->setDescription($description);
		
		return $validationError;
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(DistributionProfilePeer::STATUS) && $this->getStatus() == DistributionProfileStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	public function getConfigVersion()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CONFIG_VERSION);
	}
	
	public function incrementConfigVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_CONFIG_VERSION, $version);
	}


	public function shouldAddDistributeByType($entryType)
	{
		if($entryType == entryType::LIVE_STREAM)
			return false;
		return true;
	}
	
	public function shouldDistributeByType($entryId, $entryType)
	{
		if($entryType == entryType::LIVE_STREAM)
			return false;
		return true;
	}
	
	public function getSunriseDefaultOffset()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SUNRISE_DEFAULT_OFFSET);}	
	public function getSunsetDefaultOffset()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SUNSET_DEFAULT_OFFSET);}	
	public function getRecommendedStorageProfileForDownload()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_STORAGE_PROFILE_DOWNLOAD);}	
	public function getRecommendedDcForDownload()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_DC_DOWNLOAD);}
	public function getRecommendedDcForExecute()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_DC_EXECUTE);}
	public function getRequiredAssetDistributionRules()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_REQUIRED_ASSET_DISTRIBUTION_RULES, null, array());}
	public function getOptionalAssetDistributionRules()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_OPTIONAL_ASSET_DISTRIBUTION_RULES, null, array());}
	public function getDistributeTrigger()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DISTRIBUTE_TRIGGER, null, kDistributeTrigger::ENTRY_READY);
	}

	public function setSunriseDefaultOffset($v)					{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SUNRISE_DEFAULT_OFFSET, $v);}
	public function setSunsetDefaultOffset($v)					{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SUNSET_DEFAULT_OFFSET, $v);}
	public function setRecommendedStorageProfileForDownload($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_STORAGE_PROFILE_DOWNLOAD, $v);}
	public function setRecommendedDcForDownload($v)				{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_DC_DOWNLOAD, $v);}
	public function setRecommendedDcForExecute($v)				{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_RECOMMENDED_DC_EXECUTE, $v);}
	public function setRequiredAssetDistributionRules($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_REQUIRED_ASSET_DISTRIBUTION_RULES, $v);}
	public function setOptionalAssetDistributionRules($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_OPTIONAL_ASSET_DISTRIBUTION_RULES, $v);}
	public function setDistributeTrigger($v)			            {return $this->putInCustomData(self::CUSTOM_DATA_FIELD_DISTRIBUTE_TRIGGER, $v);}

	public function getCacheInvalidationKeys()
	{
		return array("distributionProfile:id=".strtolower($this->getId()));
	}
} // DistributionProfile

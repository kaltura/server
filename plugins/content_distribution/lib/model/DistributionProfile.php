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
abstract class DistributionProfile extends BaseDistributionProfile implements ISyncableFile
{
	const FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG = 1;
	
	const CUSTOM_DATA_FIELD_CONFIG_VERSION = "configVersion";
	
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
	 * @see content_distribution/lib/model/om/BaseDistributionProfile#setRequiredFlavorParamsIdsArray()
	 */
	public function setRequiredFlavorParamsIdsArray(array $v)
	{
		return $this->setRequiredFlavorParamsIds(implode(',', $v));
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
	
	public function getConfigVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CONFIG_VERSION);}
	public function incrementConfigVersion()	{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_CONFIG_VERSION);}
	
} // DistributionProfile

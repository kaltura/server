<?php

/**
 * Subclass for representing a row from the 'conversion_profile_2' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class conversionProfile2 extends BaseconversionProfile2 implements ISyncableFile
{
	const CONVERSION_PROFILE_NONE = -1;
	
	const CONVERSION_PROFILE_2_CREATION_MODE_MANUAL = 1;
	const CONVERSION_PROFILE_2_CREATION_MODE_KMC = 2;
	const CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC = 3;
	const CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV = 4;
	
	const FILE_SYNC_MRSS_XSL = 1;
	const FILE_SYNC_MEDIAINFO_XSL = 2;
	
	const DEFAULT_REPLACEMENT_OPTIONS = "defaultReplacementOptions";
	
	private $xsl;
	private $mediaInfoXsl;
	
	protected $isDefault;
		
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getFileSync()
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync; 
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#setFileSync()
	 */
	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_MRSS_XSL,
			self::FILE_SYNC_MEDIAINFO_XSL,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::CONVERSION_PROFILE, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		if(!$version)
		{
			if($sub_type == self::FILE_SYNC_MRSS_XSL)
				$version = $this->getVersion();
			else
				$version = $this->getMediaInfoXslVersion();		
		}
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::CONVERSION_PROFILE;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		return $key;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
			
		return $this->getId(). "_{$version}.xsl";
	}
	
	public function getVersion()
	{
		return $this->getFromCustomData("xslVersion", null, 0);
	}
	
	public function getMediaInfoXslVersion()
	{
		return $this->getFromCustomData("mediaInfoXslVersion", null, 0);
	}
	
	public function incrementXslVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getVersion(), FileSyncObjectType::CONVERSION_PROFILE, self::FILE_SYNC_MRSS_XSL);
		
		$this->putInCustomData("xslVersion", $newVersion);
	}
	
	public function incrementMediaInfoXslVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getMediaInfoXslVersion(), FileSyncObjectType::CONVERSION_PROFILE, self::FILE_SYNC_MEDIAINFO_XSL);
		
		$this->putInCustomData("mediaInfoXslVersion", $newVersion);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getVersion();
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/conversion/xsl/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/**
	 * @return string
	 */
	public function getXsl()
	{
		if (!is_null($this->xsl))
			return $this->xsl;

		$key = $this->getSyncKey(self::FILE_SYNC_MRSS_XSL);
		$this->xsl = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->xsl;
	}
	
	/**
	 * @return string
	 */
	public function getMediaInfoXslTransformation()
	{
		if (!is_null($this->mediaInfoXsl))
			return $this->mediaInfoXsl;

		$key = $this->getSyncKey(self::FILE_SYNC_MEDIAINFO_XSL);
		$this->mediaInfoXsl = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->mediaInfoXsl;
	}

	/* (non-PHPdoc)
	 * @see BaseconversionProfile2::setDeletedAt()
	 */
	public function setDeletedAt($v)
	{
		parent::setDeletedAt($v);
		parent::setStatus(ConversionProfileStatus::DELETED);
	}
	
	/* (non-PHPdoc)
	 * @see BaseconversionProfile2::setStatus()
	 */
	public function setStatus($v)
	{
		parent::setStatus($v);
		if($v == ConversionProfileStatus::DELETED)
			parent::setDeletedAt(time());
	}
	
	public function setIsDefault($v)
	{
		$this->isDefault = (bool)$v;
	}
	
	public function getIsDefault()
	{
		if ($this->isDefault === null)
		{
			if ($this->isNew())
				return false;
				
			$partner = PartnerPeer::retrieveByPK($this->partner_id);
			if ($partner)
			{
				if ($this->getType() == ConversionProfileType::MEDIA && $this->getId() == $partner->getDefaultConversionProfileId())
					$this->isDefault = true;
				elseif ($this->getType() == ConversionProfileType::LIVE_STREAM && $this->getId() == $partner->getDefaultLiveConversionProfileId())
					$this->isDefault = true;
				else
					$this->isDefault = false;
			}
		}
		
		return $this->isDefault;
	}
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isColumnModified(conversionProfile2Peer::DELETED_AT) && $this->isDefault === true)
		{
			throw new Exception("Default conversion profile can't be deleted");
		}
		parent::save($con);
		
		// set this conversion profile as partners default
		$partner = PartnerPeer::retrieveByPK($this->partner_id);
		if ($partner && $this->isDefault === true)
		{
			if($this->getType() == ConversionProfileType::MEDIA)
				$partner->setDefaultConversionProfileId($this->getId());
				
			if($this->getType() == ConversionProfileType::LIVE_STREAM)
				$partner->setDefaultLiveConversionProfileId($this->getId());
				
			$partner->save();
		}
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseconversionProfile2#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(conversionProfile2Peer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseconversionProfile2#preInsert()
	 */
	public function preInsert (PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::preInsert($con);

		if ($this->getSystemName())
		{
			$existingConversionProfile = conversionProfile2Peer::retrieveByPartnerIdAndSystemName($this->getPartnerId(), $this->getSystemName(), $this->getType());
			if ($existingConversionProfile)
			{
				throw new kCoreException("Conversion profile with system name [" . $this->getSystemName() . "] already exists");
			}
		}
		
		return parent::preInsert($con);
	}
	
	public function copyInto($copyObj, $deepCopy = false)
	{
		/* @var $copyObj conversionProfile2 */
		
		parent::copyInto($copyObj, $deepCopy);
		$copyObj->setType($this->getType());
		$copyObj->setMediaParserType($this->getMediaParserType());
		$copyObj->setIsDefault($this->getIsDefault());
	}
	
	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this conversionProfile2 is new, it will return
	 * an empty collection; or if this conversionProfile2 has previously
	 * been saved, it will retrieve related flavorParamsConversionProfiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in conversionProfile2.
	 */
	public function getflavorParamsConversionProfilesJoinflavorParams($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		return $this->getflavorParamsConversionProfilesJoinassetParams($criteria, $con, $join_behavior);
	}

	public function getStorageProfileId()
	{
		return $this->getFromCustomData('storageProfileId');
	}
	
	public function setStorageProfileId($v)
	{
		$this->putInCustomData('storageProfileId', $v);
	}
	
	public function setMediaParserType($mediaParserType)
	{
		$this->putInCustomData('mediaParserType', $mediaParserType);
	}
	
	public function getMediaParserType()
	{
		return $this->getFromCustomData('mediaParserType', null, mediaParserType::MEDIAINFO);
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("conversionProfile2:partnerId=".strtolower($this->getPartnerId()));
	}
	
	public function getRequiredCopyTemplatePermissions ()
	{
		return $this->getFromCustomData('requiredCopyTemplatePermissions', null, array());
	}
	
	public function setRequiredCopyTemplatePermissions ($v)
	{
		if(!is_array($v))
			$v = array_map('trim', explode(',', $v));
			
		$this->putInCustomData('requiredCopyTemplatePermissions', $v);
	}
	
	public function getCalculateComplexity()
	{
		return $this->getFromCustomData('calculateComplexity', null, true);
	}
	
	public function setCalculateComplexity($v)
	{
		$this->putInCustomData('calculateComplexity', $v);
	}
	
	/*
	 * Defines the tags that should be used to define 'collective'/group/multi-flavor processing,
	 * like 'mbr' or 'ism'
	 */
	public function getCollectionTags() { return $this->getFromCustomData('collectionTags', null, 'mbr,ism'); }
	public function setCollectionTags($v) {	$this->putInCustomData('collectionTags', $v); }

	/*
	 * JSON string with array of "condition,profile-id" pairs. For example - 
	 * 	'[{"profileId":"11","condition":"containerFormat==wmv"}]'
	 *	For all WMV sources the conv-prof 11 will be applied (overriding the original conv.prof)"
	 */
	public function getConditionalProfiles() { return $this->getFromCustomData('conditionalProfiles', null, null); }
	public function setConditionalProfiles($v) { $this->putInCustomData('conditionalProfiles', $v); }
	
	/*
	 * When set, the ExtractMedia job should attempt to detect the source file GOP interval 
	 * using the 'detectGOP' value as the max calculated period
	 */
	public function getDetectGOP() { return $this->getFromCustomData('detectGOP', null, 0); }
	public function setDetectGOP($v) { $this->putInCustomData('detectGOP', $v); }

	/*
	 * When set, the replacement process should use the CP replacement options by default, if no others are passed
	 * 
	 */
	public function getDefaultReplacementOptions() {return $this->getFromCustomData(self::DEFAULT_REPLACEMENT_OPTIONS, null, 0);}
	public function setDefaultReplacementOptions($v) {$this->putInCustomData(self::DEFAULT_REPLACEMENT_OPTIONS, $v);}

}

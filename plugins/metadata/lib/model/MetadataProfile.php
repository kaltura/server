<?php


/**
 * Skeleton subclass for representing a row from the 'metadata_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataProfile extends BaseMetadataProfile implements ISyncableFile
{
	const FILE_SYNC_METADATA_DEFINITION = 1;
	const FILE_SYNC_METADATA_VIEWS = 2;
	const FILE_SYNC_METADATA_XSLT = 3;
	
	const STATUS_ACTIVE = 1;
	const STATUS_DEPRECATED = 2;
	const STATUS_TRANSFORMING = 3;
	
	const CUSTOM_DATA_METADATA_XSLT_VERSION = 'metadata_xslt_version';
	
	/* (non-PHPdoc)
	 * @see metadata/lib/model/om/BaseMetadata#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->incrementVersion();
		$this->incrementViewsVersion();
		$this->incrementXsltVersion();
		return parent::preInsert($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseMetadataProfile#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(MetadataProfilePeer::STATUS) && $this->getStatus() == self::STATUS_DEPRECATED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}

	public function incrementVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getVersion(), FileSyncObjectType::METADATA_PROFILE, self::FILE_SYNC_METADATA_DEFINITION);
		
		$this->setVersion($newVersion);
	}

	public function incrementViewsVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getViewsVersion(), FileSyncObjectType::METADATA_PROFILE, self::FILE_SYNC_METADATA_VIEWS);
		
		$this->setViewsVersion($newVersion);
	}
	
    public function incrementXsltVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getXsltVersion(), FileSyncObjectType::METADATA_PROFILE, self::FILE_SYNC_METADATA_XSLT);
		
		$this->setXsltVersion($newVersion);
	}
	
	protected function setXsltVersion($version)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_METADATA_XSLT_VERSION, $version);
	}
	
    public function getXsltVersion()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_XSLT_VERSION);
	}	
	
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_METADATA_DEFINITION,
			self::FILE_SYNC_METADATA_VIEWS,
			self::FILE_SYNC_METADATA_XSLT,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::METADATA_PROFILE, $sub_type, $valid_sub_types);
	}
	
	/**
	 * @param int $sub_type
	 * @param int $version
	 * 
	 * @return int
	 */
	private function getFileSyncSubTypeVersion($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if($version)
			return $version;
	
		switch($sub_type)
		{
			case self::FILE_SYNC_METADATA_DEFINITION:
				return $this->getVersion();
				
			case self::FILE_SYNC_METADATA_VIEWS:
				return $this->getViewsVersion();
				
			case self::FILE_SYNC_METADATA_XSLT:
			    return $this->getXsltVersion();
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		$version = $this->getFileSyncSubTypeVersion($sub_type, $version);
		
		return kMetadataManager::getObjectTypeName($this->getObjectType()) . "_" . $this->getId() . "_{$sub_type}_{$version}.xml";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		$version = $this->getFileSyncSubTypeVersion($sub_type, $version);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/metadata/profile/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}
	
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
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		$version = $this->getFileSyncSubTypeVersion($sub_type, $version);
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::METADATA_PROFILE;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("metadataProfile:id=".strtolower($this->getId()), "metadataProfile:partnerId=".strtolower($this->getPartnerId()));
	}
} // MetadataProfile

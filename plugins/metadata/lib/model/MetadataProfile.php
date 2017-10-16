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
	const CUSTOM_DATA_DISABLE_REINDEXING = 'disable_reindexing';
	
	private $xsdData = null;
	private $viewsData = null;
	private $xsltData = null;
	
	/* (non-PHPdoc)
	 * @see metadata/lib/model/om/BaseMetadata#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->incrementVersion();
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
	
		/* (non-PHPdoc)
	 * @see BaseMetadataProfile::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
	    if($this->xsdData)
	        $this->incrementFileSyncVersion();
	    
	    if($this->viewsData)
	        $this->incrementViewsVersion();
	    
	    if($this->xsltData)
	        $this->incrementXsltVersion();
	    
	    return parent::preSave($con);
	}
	
		/* (non-PHPdoc)
	 * @see BaseMetadataProfile::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
    	if($this->xsdData)
    	{
        	$key = $this->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
            kFileSyncUtils::file_put_contents($key, $this->xsdData);
                
            kMetadataManager::parseProfileSearchFields($this->getPartnerId(), $this);
    	}
    	         
        if($this->viewsData)
    	{
			$key = $this->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
			kFileSyncUtils::file_put_contents($key, $this->viewsData);
		}
            
		if($this->xsltData)
		{
			$key = $this->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
			kFileSyncUtils::file_put_contents($key, $this->xsltData);
		}
	
	    return parent::postSave($con);
	}
	
	public function incrementVersion()
	{
		$this->setVersion($this->getFileSyncVersion());
	}
	
	public function incrementFileSyncVersion() {
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getFileSyncVersion(), FileSyncObjectType::METADATA_PROFILE, self::FILE_SYNC_METADATA_DEFINITION);
		$this->setFileSyncVersion($newVersion);
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

	public function setDisableReIndexing($value)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DISABLE_REINDEXING, (bool)$value);
	}

	public function getDisableReIndexing()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DISABLE_REINDEXING, null, false);
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
				return $this->getFileSyncVersion();
				
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
	
	public function getPreviousFileSyncVersion() {
		$this->getFromCustomData('previousFileSyncVersion');
	}
	
	public function setPreviousFileSyncVersion($v) {
		$this->putInCustomData('previousFileSyncVersion', $v);
	}
	
	public function setFileSyncVersion($v) {
		$this->setPreviousFileSyncVersion($this->getFileSyncVersion());
		parent::setFileSyncVersion($v);
	}
	
	public function setXsdData($xsdData)
	{
	    $this->xsdData = $xsdData;
	}
	
	public function setViewesData($viewsData)
	{
	    $this->viewsData = $viewsData;
	}
	
	public function setXsltData($xsltData)
	{
	    $this->xsltData = $xsltData;
	}

	public function getFileSyncVersion()
	{
		$fileSyncVersion = parent::getFileSyncVersion();
		if (is_null($fileSyncVersion))
		{
			return $this->getVersion();
		}
		return $fileSyncVersion;

	}


	public function getMetadataFieldsKeys()
	{
		$metadataFields = MetadataProfileFieldPeer::retrieveAllActiveByMetadataProfileId($this->id);
		$keys = array();
		foreach ($metadataFields as $metadataField)
			$keys[] = $metadataField->getKey();
		return $keys;
	}

	/**
	 * Copy current permission to the given partner.
	 * @param int $partnerId
	 * @return MetadataProfile
	 */
	public function copyToPartner($partnerId)
	{
		$newMetaDataProfile = $this->copy(true);
		$key = $this->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$newMetaDataProfile->setXsdData(kFileSyncUtils::file_get_contents($key, true, false));
		$newMetaDataProfile->setPartnerId($partnerId);
		return $newMetaDataProfile;
	}
} // MetadataProfile

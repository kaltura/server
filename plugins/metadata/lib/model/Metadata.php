<?php

/**
 * Skeleton subclass for representing a row from the 'metadata' table.
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
class Metadata extends BaseMetadata implements IIndexable, ISyncableFile, IRelatedObject
{
	const FILE_SYNC_METADATA_DATA = 1;
	
	const STATUS_VALID = 1;
	const STATUS_INVALID = 2;
	const STATUS_DELETED = 3;
	
	/**
	 * @var MetadataProfile
	 */
	protected $aMetadataProfile;

	/**
	 * Metadata is counted as new during the metadata.add API
	 *
	 * @var bool
	 */
	protected $likeNew = false;
	
	/* (non-PHPdoc)
	 * @see metadata/lib/model/om/BaseMetadata#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->incrementVersion();
		return parent::preInsert($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseMetadata#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);

		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if($this->isColumnModified(MetadataPeer::STATUS) && $this->getStatus() == self::STATUS_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));

		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseMetadata#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);

		kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}

	public function incrementVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getVersion(), FileSyncObjectType::METADATA, self::FILE_SYNC_METADATA_DATA);
		$this->setVersion($newVersion);
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_METADATA_DATA,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::METADATA, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
			
		return kMetadataManager::getObjectTypeName($this->getObjectType()) . "_" . $this->getObjectId() . "_" . $this->getId() . "_$version.xml";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null, $externalPath = false )
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getVersion();

		$path = "/content/metadata/data/";
		if ($externalPath)
		{
			$path = "/data/";
		}
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path .= "/$dir/" .$this->generateFileName($sub_type, $version);

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
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::METADATA;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	/**
	 * @return MetadataProfile
	 */
	public function getMetadataProfile()
	{
		if ($this->aMetadataProfile === null && $this->metadata_profile_id) 
			$this->aMetadataProfile = MetadataProfilePeer::retrieveByPK($this->metadata_profile_id);
			
		return $this->aMetadataProfile;
	}

	public function getCacheInvalidationKeys()
	{
		return array("metadata:objectId=".strtolower($this->getObjectId()));
	}

	public function getSphinxMatchOptimizations() {
		$objectName = $this->getIndexObjectName();
		return $objectName::getSphinxMatchOptimizations($this);
	}

	/**
	 * @return int
	 */
	public function getIntId()
	{
		return $this->getId();
	}

	/**
	 * @return string
	 */
	public function getEntryId()
	{
		if ($this->getObjectType() == MetadataObjectType::ENTRY)
			return $this->getObjectId();
		else
			return null;
	}

	/**
	 * @return entry
	 */
	public function getEntry()
	{
		if ($this->getObjectType() == MetadataObjectType::ENTRY)
			return entryPeer::retrieveByPk($this->object_id);
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getIndexObjectName()
	{
		return "MetadataIndex";
	}

	/**
	 * Index the object in the search engine
	 */
	public function indexToSearchIndex()
	{
		if ($this->getObjectType() == MetadataObjectType::DYNAMIC_OBJECT)
			kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	public function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(MetadataIndex::getObjectIndexName());
	}

	/**
	 * @return boolean
	 */
	public function isLikeNew()
	{
		return $this->likeNew;
	}

	/**
	 * @param boolean $likeNew
	 */
	public function setLikeNew($likeNew)
	{
		$this->likeNew = $likeNew;
	}
} // Metadata

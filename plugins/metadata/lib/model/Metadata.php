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
 * @package    lib.model
 */
class Metadata extends BaseMetadata implements ISyncableFile
{
	const FILE_SYNC_METADATA_DATA = 1;
	
	const STATUS_VALID = 1;
	const STATUS_INVALID = 2;
	const STATUS_DELETED = 3;
	
	const TYPE_ENTRY = 1;
	
	/**
	 * @var MetadataProfile
	 */
	protected $aMetadataProfile;
	
	/* (non-PHPdoc)
	 * @see metadata/lib/model/om/BaseMetadata#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setVersion(1);
		return parent::preInsert($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseMetadata#preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(MetadataPeer::STATUS) && $this->getStatus() == self::STATUS_DELETED)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return parent::preUpdate($con);
	}

	public function incrementVersion()
	{
		$this->setVersion($this->getVersion() + 1);
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
			throw new FileSyncException(FileSync::FILE_SYNC_OBJECT_TYPE_METADATA, $sub_type, $valid_sub_types);
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
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getVersion();
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/metadata/data/$dir/" . $this->generateFileName($sub_type, $version);

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
		$key->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_METADATA;
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
			$this->aMetadataProfile = MetadataProfilePeer::retrieveById($this->metadata_profile_id);
			
		return $this->aMetadataProfile;
	}
} // Metadata

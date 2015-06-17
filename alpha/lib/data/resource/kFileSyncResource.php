<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different file in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync.
 *
 * @package Core
 * @subpackage model.data
 */
class kFileSyncResource extends kContentResource 
{
	/**
	 * The object type of the file sync object 
	 * @var int
	 */
	private $fileSyncObjectType;
	
	/**
	 * The object sub-type of the file sync object 
	 * @var int
	 */
	private $objectSubType;
	
	/**
	 * The object id of the file sync object 
	 * @var string
	 */
	private $objectId;
	
	/**
	 * The version of the file sync object 
	 * @var string
	 */
	private $version;

	/**
	 * The original entry ID, if exists
	 * @var string
	 */
	private $originEntryId;

	/**
	 * @return string
	 */
	public function getOriginEntryId()
	{
		return $this->originEntryId;
	}

	/**
	 * @param string $originEntryId
	 */
	public function setOriginEntryId($originEntryId)
	{
		$this->originEntryId = $originEntryId;
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		if($this->fileSyncObjectType == FileSyncObjectType::ENTRY)
			return $this->objectId;
			
		$object = kFileSyncObjectManager::retrieveObject($this->fileSyncObjectType, $this->objectId);
		if(method_exists($object, 'getEntryId'))
			return $object->getEntryId();
		 
		return null;
	}
	
	/**
	 * @return the $fileSyncObjectType
	 */
	public function getFileSyncObjectType()
	{
		return $this->fileSyncObjectType;
	}

	/**
	 * @return the $objectSubType
	 */
	public function getObjectSubType()
	{
		return $this->objectSubType;
	}

	/**
	 * @return the $objectId
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @return the $version
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param int $fileSyncObjectType
	 */
	public function setFileSyncObjectType($fileSyncObjectType)
	{
		$this->fileSyncObjectType = $fileSyncObjectType;
	}

	/**
	 * @param int $objectSubType
	 */
	public function setObjectSubType($objectSubType)
	{
		$this->objectSubType = $objectSubType;
	}

	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}
}
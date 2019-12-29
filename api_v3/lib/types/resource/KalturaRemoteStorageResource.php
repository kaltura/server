<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, the media file won't be downloaded but a file sync object of URL type will point to the media URL.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaRemoteStorageResource extends KalturaUrlResource
{
	/**
	 * ID of storage profile to be associated with the created file sync, used for file serving URL composing. 
	 * @var int
	 */
	public $storageProfileId;
	
	private static $map_between_objects = array('storageProfileId');
	
	/* (non-PHPdoc)
	 * @see KalturaUrlResource::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('storageProfileId');
		
		$storageProfile = StorageProfilePeer::retrieveByIdAndPartnerId($this->storageProfileId, kCurrentContext::getCurrentPartnerId());
		if(!$storageProfile)
			throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $this->storageProfileId);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaUrlResource::toObject()
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kRemoteStorageResource();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
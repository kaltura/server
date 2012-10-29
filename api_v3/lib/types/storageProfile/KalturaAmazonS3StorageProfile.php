<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAmazonS3StorageProfile extends KalturaStorageProfile
{
	/**
	 * @var bool
	 */
	public $filesPermissionPublicInS3;
	
	private static $map_between_objects = array
	(
		"filesPermissionPublicInS3",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject ($object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new AmazonS3StorageProfile();
				
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		$dbFilesPermissionPublicInS3 = $object_to_fill->getFromCustomData(AmazonS3StorageProfile::CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3);
		if (is_null($dbFilesPermissionPublicInS3))
			$dbFilesPermissionPublicInS3 = false;
		$object_to_fill->putInCustomData(AmazonS3StorageProfile::CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3, $dbFilesPermissionPublicInS3);
		
		return $object_to_fill;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject ( $source_object  )
	{
		KalturaLog::DEBUG("WE ARE IN KalturaAmazonS3StorageProfile->fromObject ".get_class($source_object));
	    parent::fromObject($source_object);
	    
	    $this->filesPermissionPublicInS3 = $source_object->getFromCustomData(AmazonS3StorageProfile::CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3);
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		KalturaLog::DEBUG("I'm here!");
		if(is_null($object_to_fill))
			$object_to_fill = new AmazonS3StorageProfile();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
}
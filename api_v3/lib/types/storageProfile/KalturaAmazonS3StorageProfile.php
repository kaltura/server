<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAmazonS3StorageProfile extends KalturaStorageProfile
{
	/**
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */
	public $filesPermissionInS3;
	
	/**
	 * @var string
	 */
	public $s3Region;
	
	/**
	 * @var string
	 */
	public $sseType;
	
	/**
	 * @var string
	 */
	public $sseKmsKeyId;
	
	/**
	 * @var string
	 */
	public $signatureType;
	
	/**
	 * @var string
	 */
	public $endPoint;
	
	private static $map_between_objects = array
	(
		"filesPermissionInS3",
		"s3Region",
		"sseType",
		"sseKmsKeyId",
		"signatureType",
		"endPoint",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill)){
			$object_to_fill = new AmazonS3StorageProfile();
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		// Validate user name and password were provided to distinguish between external stoarge to internal
		$this->validatePropertyNotNull("storageUsername");
		$this->validatePropertyNotNull("storagePassword");
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
}
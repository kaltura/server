<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageProfile extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $updatedAt;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $desciption;
	
	/**
	 * @var KalturaStorageProfileStatus
	 */
	public $status;
	
	/**
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $storageUrl;
	
	/**
	 * @var string
	 */
	public $storageBaseDir;
	
	/**
	 * @var string
	 */
	public $storageUsername;
	
	/**
	 * @var string
	 */
	public $storagePassword;
	
	/**
	 * @var bool
	 */
	public $storageFtpPassiveMode;
	
	/**
	 * @var string
	 */
	public $deliveryHttpBaseUrl;
	
	/**
	 * @var string
	 */
	public $deliveryRmpBaseUrl;
	
	/**
	 * @var string
	 */
	public $deliveryIisBaseUrl;
	
	/**
	 * @var int
	 */
	public $minFileSize;
	
	/**
	 * @var int
	 */
	public $maxFileSize;
	
	/**
	 * @var string
	 */
	public $flavorParamsIds;
	
	/**
	 * @var int
	 */
	public $maxConcurrentConnections;
	
	/**
	 * @var string
	 */
	public $pathManagerClass;
	
	/**
	 * @var string
	 */
	public $urlManagerClass;
	
	/**
	 * TODO - remove after events manager is implemented
	 * No need to create enum for temp field
	 * 
	 * @var int
	 */
	public $trigger;
	
	private static $map_between_objects = array
	(
		"id",
		"createdAt",
		"updatedAt",
		"partnerId",
		"name",
		"desciption",
		"status",
		"protocol",
		"storageUrl",
		"storageBaseDir",
		"storageUsername",
		"storagePassword",
		"storageFtpPassiveMode",
		"deliveryHttpBaseUrl",
		"deliveryRmpBaseUrl",
		"deliveryIisBaseUrl",
		"minFileSize",
		"maxFileSize",
		"flavorParamsIds",
		"maxConcurrentConnections",
		"pathManagerClass",
		"urlManagerClass",
		"trigger", // TODO - remove after events manager is implemented
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	
	/**
	 * @param StorageProfile $object_to_fill
	 * @param array $props_to_skip
	 * @return StorageProfile
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new StorageProfile();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new StorageProfile();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
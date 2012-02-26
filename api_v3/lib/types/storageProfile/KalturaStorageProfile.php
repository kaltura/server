<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageProfile extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * @var string
	 */
	public $desciption;
	
	/**
	 * @var KalturaStorageProfileStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaStorageProfileProtocol
	 * @filter eq,in
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
	 * @var KalturaKeyValueArray
	 */
	public $pathManagerParams;
	
	/**
	 * @var string
	 */
	public $urlManagerClass;
	
	/**
	 * @var KalturaKeyValueArray
	 */
	public $urlManagerParams;
	
	/**
	 * No need to create enum for temp field
	 * 
	 * @var int
	 */
	public $trigger;
	
	/**
	 * Delivery Priority
	 * 
	 * @var int
	 */
	public $deliveryPriority;
	
	/**
	 * 
	 * @var KalturaStorageProfileDeliveryStatus
	 */
	public $deliveryStatus;
	
	/**
	 * 
	 * @var string
	 */
	public $rtmpPrefix;
	
	private static $map_between_objects = array
	(
		"id",
		"createdAt",
		"updatedAt",
		"partnerId",
		"name",
		"systemName",
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
		"trigger",
		"deliveryPriority",
		"deliveryStatus",
		"rtmpPrefix"
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
		
		
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		// url manager params
		$dbUrlManagerParams = $object_to_fill->getUrlManagerParams();
		if (!is_null($this->urlManagerParams) && count($this->urlManagerParams) > 0)
		{
    		foreach ($this->urlManagerParams as $param)
    		{
    		    $dbUrlManagerParams[$param->key] = $param->value;
    		}
		}
		$object_to_fill->setUrlManagerParams($dbUrlManagerParams);
		
		// path manager params
		$dbPathManagerParams = $object_to_fill->getPathManagerParams();
		if (!is_null($this->pathManagerParams) && count($this->pathManagerParams) > 0)
		{
    		foreach ($this->pathManagerParams as $param)
    		{
    		    $dbPathManagerParams[$param->key] = $param->value;
    		}
		}
		$object_to_fill->setPathManagerParams($dbPathManagerParams);
		
		return $object_to_fill;
	}
	
	public function fromObject ( $source_object  )
	{
	    parent::fromObject($source_object);
	    
	    $this->urlManagerParams = KalturaKeyValueArray::fromKeyValueArray($source_object->getUrlManagerParams());
	    $this->pathManagerParams = KalturaKeyValueArray::fromKeyValueArray($source_object->getPathManagerParams());
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}
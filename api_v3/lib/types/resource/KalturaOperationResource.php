<?php
/**
 * A resource that perform operation (transcoding, clipping, cropping) before the flavor is ready.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaOperationResource extends KalturaContentResource
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * @var KalturaContentResource
	 */
	public $resource;
	
	/**
	 * @var KalturaOperationAttributesArray
	 */
	public $operationAttributes;
	
	/**
	 * ID of alternative asset params to be used instead of the system default flavor params 
	 * @var int
	 */
	public $assetParamsId;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('resource');
		
		if(!($this->resource instanceof KalturaEntryResource) && !($this->resource instanceof KalturaAssetResource))
			throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($this->resource));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResource::validateEntry()
	 */
	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
		
		$this->resource->validateEntry($dbEntry);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResource::entryHandled()
	 */
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		$this->resource->entryHandled($dbEntry);
	}
	
	private static $map_between_objects = array('assetParamsId');
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);
		
		if(is_null($this->operationAttributes) || !count($this->operationAttributes))
			return $this->resource->toObject();
		
		if(!$object_to_fill)
			$object_to_fill = new kOperationResource();
		
		$operationAttributes = array();
		foreach($this->operationAttributes as $operationAttributesObject)
			$operationAttributes[] = $operationAttributesObject->toObject();
		
		$object_to_fill->setOperationAttributes($operationAttributes);
		$object_to_fill->setResource($this->resource->toObject());
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
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

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('resource');
    	
		if(!($this->resource instanceof KalturaEntryResource) && !($this->resource instanceof KalturaAssetResource))
			throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($this->resource));
			 
		$this->resource->validateEntry($dbEntry);
	}
	
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		$this->resource->entryHandled($dbEntry);
	}
	
	private static $map_between_objects = array
	(
		'assetParamsId',
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
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
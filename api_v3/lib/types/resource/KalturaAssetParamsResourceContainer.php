<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParamsResourceContainer extends KalturaResource 
{
	/**
	 * The content resource to associate with asset params
	 * @var KalturaContentResource
	 */
	public $resource;
	
	/**
	 * The asset params to associate with the reaource
	 * @var int
	 */
	public $assetParamsId;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('resource');
    	$this->validatePropertyNotNull('assetParamsId');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kAssetParamsResourceContainer();
			
		if($this->resource)
			$object_to_fill->setResource($this->resource->toObject());
			
		$object_to_fill->setAssetParamsId($this->assetParamsId);
		return $object_to_fill;
	}
}
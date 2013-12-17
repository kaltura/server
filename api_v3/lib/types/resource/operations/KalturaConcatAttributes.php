<?php
/**
 * Concat operation attributes
 * 
 * @package api
 * @subpackage objects
 */
class KalturaConcatAttributes extends KalturaOperationAttributes
{
	/**
	 * The resource to be concatenated
	 * @var KalturaDataCenterContentResource
	 */
	public $resource;

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kConcatAttributes();
			
		$resource = $this->resource->toObject();
		if($resource instanceof kLocalFileResource)
			$object_to_fill->setFilePath($resource->getLocalFilePath());
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
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
		throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($this));
	}
}
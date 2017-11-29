<?php
/**
 * @package api
 * @subpackage object
 */
class KalturaOutputFormatItem extends KalturaObject
{
	/**
	 *  @var KalturaVendorCatalogItemOutputFormat
	 */
	public $outputFormat;
	
	private static $map_between_objects = array (
		'outputFormat',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kOutputFormatItem();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}
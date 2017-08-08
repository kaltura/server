<?php
/**
 * Info about Asset type
 * 
 * @see KalturaStringArray
 * @package api
 * @subpackage objects
 */
class KalturaAssetTypeInfo extends KalturaObject
{
	/**
	 * Asset Type
	 * 
	 * @var KalturaAssetType
	 */
	public $type;
    
	private static $mapBetweenObjects = array
	(
		"type",
	);

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new assetTypeInfo();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}

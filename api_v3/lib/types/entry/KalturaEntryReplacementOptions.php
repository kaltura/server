<?php
/**
 * Advanced configuration for entry replacement process
 * @package api
 * @subpackage objects
 */
class KalturaEntryReplacementOptions extends KalturaObject
{
	/**
	 * If true manually created thumbnails will not be deleted on entry replacement
	 * @var int
	 */
	public $keepManualThumbnails;
	
	private static $mapBetweenObjects = array
	(
		'keepManualThumbnails',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kEntryReplacementOptions();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
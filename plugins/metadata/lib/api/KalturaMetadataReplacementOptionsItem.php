<?php
/**
 * Advanced metadata configuration for entry replacement process
 * @package plugins.metadata
 * @subpackage api
 */
class KalturaMetadataReplacementOptionsItem extends KalturaPluginReplacementOptionsItem 
{
	/**
	 * If true custom-metadata transferred to temp entry on entry replacement
	 * @var bool
	 */
	public $shouldCopyMetadata;

	private static $mapBetweenObjects = array
	(
		'shouldCopyMetadata',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return self::$mapBetweenObjects;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kMetadataReplacementOptionsItem();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}

<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaTranslationEntryVendorTaskJobData extends KalturaCaptionsEntryVendorTaskJobData
{
	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $targetLanguage;
	
	private static $map_between_objects = array (
		'targetLanguage',
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
			$dbObject = new kTranslationEntryVendorTaskJobData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}
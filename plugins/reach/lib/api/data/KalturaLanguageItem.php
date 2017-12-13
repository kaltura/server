<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaLanguageItem extends KalturaObject
{
	/**
	 *  @var KalturaLanguage
	 */
	public $language;
	
	private static $map_between_objects = array (
		'language',
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
			$dbObject = new kLanguageItem();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}
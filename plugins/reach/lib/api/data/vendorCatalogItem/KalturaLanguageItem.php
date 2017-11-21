<?php
/**
 * @package api
 * @subpackage object
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
}
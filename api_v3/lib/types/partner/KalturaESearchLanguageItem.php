<?php
/**
 * @package api
 * @subpackage object
 */
class KalturaESearchLanguageItem extends KalturaObject
{
	/**
	 *  @var KalturaESearchLanguage
	 */
	public $eSerachLanguage;

	private static $map_between_objects = array(
		'eSerachLanguage',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}



}
?>
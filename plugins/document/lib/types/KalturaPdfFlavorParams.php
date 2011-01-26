<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaPdfFlavorParams extends KalturaFlavorParams 
{
	/**
	 * @var bool
	 */
	public $readonly;
	
	private static $map_between_objects = array
	(
		'readonly',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
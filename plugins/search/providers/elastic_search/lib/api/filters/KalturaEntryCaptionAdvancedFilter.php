<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.filters
 */
class KalturaEntryCaptionAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $hasCaption;

	private static $map_between_objects = array
	(
		"hasCaption",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
		{
			$object_to_fill = new kEntryCaptionAdvancedFilter();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}

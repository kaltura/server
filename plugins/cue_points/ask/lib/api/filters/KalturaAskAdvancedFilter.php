<?php
/**
 * @package plugins.ask
 * @subpackage api.filters
 */
class KalturaAskAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAsk;

	private static $map_between_objects = array
	(
		"isAsk",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kAskAdvancedFilter();

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}

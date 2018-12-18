<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
abstract class KalturaBeaconSearchOrderBy extends KalturaObject
{
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchOrderBy();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
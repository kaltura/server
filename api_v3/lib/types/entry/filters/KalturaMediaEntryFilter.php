<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaEntryFilter extends KalturaMediaEntryBaseFilter
{
	static private $map_between_objects = array
	(
		"sourceTypeEqual" => "_eq_source",
		"sourceTypeNotEqual" => "_not_source",
		"sourceTypeIn" => "_in_source",
		"sourceTypeNotIn" => "_notin_source",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}

<?php
/**
 * @package plugins.externalMedia
 * @subpackage api.filters
 */
class KalturaExternalMediaEntryFilter extends KalturaExternalMediaEntryBaseFilter
{
	static private $map_between_objects = array
	(
		"externalSourceTypeEqual" => "_like_plugins_data",
		"externalSourceTypeIn" => "_mlikeor_plugins_data",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaExternalMediaEntryFilter::$map_between_objects);
	}
}

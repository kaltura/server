<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaMetadataProfileFilter extends KalturaMetadataProfileBaseFilter
{
	private $map_between_objects = array
	(
		"metadataObjectTypeEqual" => "_eq_object_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}

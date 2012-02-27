<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaMetadataFilter extends KalturaMetadataBaseFilter
{	
	private $map_between_objects = array
	(
		"metadataObjectTypeEqual" => "_eq_object_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
	
	protected function validate()
	{
		$this->validatePropertyNotNull('metadataObjectTypeEqual');
	}
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$this->validate();
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}

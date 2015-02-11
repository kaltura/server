<?php
/**
 * @package api
 * @subpackage api.filters
 */
class KalturaFileAssetFilter extends KalturaFileAssetBaseFilter
{
	static private $map_between_objects = array
	(
		"fileAssetObjectTypeEqual" => "_eq_object_type",
	);

	/* (non-PHPdoc)
	 * @see KalturaFileAssetBaseFilter::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new fileAssetFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$this->validatePropertyNotNull('fileAssetObjectTypeEqual');
		$this->validatePropertyNotNull(array('objectIdEqual', 'objectIdIn', 'idIn', 'idEqual'));
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}

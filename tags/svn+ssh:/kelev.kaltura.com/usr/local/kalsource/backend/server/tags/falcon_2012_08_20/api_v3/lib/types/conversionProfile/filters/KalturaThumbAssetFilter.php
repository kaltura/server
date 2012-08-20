<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaThumbAssetFilter extends KalturaThumbAssetBaseFilter
{
	private $map_between_objects = array
	(
		"thumbParamsIdEqual" => "_eq_flavor_params_id",
		"thumbParamsIdIn" => "_in_flavor_params_id",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}

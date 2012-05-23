<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBulkUploadFilter extends KalturaBulkUploadBaseFilter
{
    private $map_between_objects = array
	(
		"bulkUploadObjectTypeEqual" => "_eq_param_1",
		"bulkUploadObjectTypeIn" => "_in_param_1",
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}

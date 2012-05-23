<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaBulkUploadBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"bulkUploadObjectTypeEqual" => "_eq_bulk_upload_object_type",
		"bulkUploadObjectTypeIn" => "_in_bulk_upload_object_type",
	);

	private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * @var string
	 */
	public $bulkUploadObjectTypeEqual;

	/**
	 * @var string
	 */
	public $bulkUploadObjectTypeIn;
}

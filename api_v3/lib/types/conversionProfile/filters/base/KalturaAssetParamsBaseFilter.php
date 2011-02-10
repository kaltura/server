<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaAssetParamsBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"isSystemDefaultEqual" => "_eq_is_system_default",
		"formatEqual" => "_eq_format",
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
	 * 
	 * 
	 * @var KalturaNullableBoolean
	 */
	public $isSystemDefaultEqual;

	/**
	 * 
	 * 
	 * @var KalturaContainerFormat
	 */
	public $formatEqual;
}

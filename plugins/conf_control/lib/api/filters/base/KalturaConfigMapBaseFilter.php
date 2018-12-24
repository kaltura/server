<?php
/**
 * @package plugins.confControl
 * @relatedService ConfControlService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaConfigMapBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"nameEqual" => "_eq_map_name",
		"relatedHostEqual" => "_eq_host_name",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var string
	 */
	public $nameEqual;

	/**
	 * @var string
	 */
	public $relatedHostEqual;
}

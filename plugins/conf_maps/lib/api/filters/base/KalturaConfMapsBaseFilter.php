<?php
/**
 * @package plugins.confMaps
 * @relatedService ConfMapsService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaConfMapsBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"nameEqual" => "_eq_name",
		"relatedHostEqual" => "_eq_related_host",
		"versionEqual" => "_eq_version"
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

	/**
	 * @var int
	 */
	public $versionEqual;

}

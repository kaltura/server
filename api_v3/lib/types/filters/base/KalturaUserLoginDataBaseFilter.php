<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserLoginDataBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"loginEmailEqual" => "_eq_login_email",
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
	public $loginEmailEqual;
}

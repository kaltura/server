<?php
/**
 * @package api
 * @relatedService UserService
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserBaseFilter extends KalturaBaseUserFilter
{
	static private $map_between_objects = array
	(
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"isAdminEqual" => "_eq_is_admin",
		"firstNameStartsWith" => "_likex_first_name",
		"lastNameStartsWith" => "_likex_last_name",
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
	 * @var KalturaUserType
	 */
	public $typeEqual;

	/**
	 * @var string
	 */
	public $typeIn;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAdminEqual;

	/**
	 * @var string
	 */
	public $firstNameStartsWith;

	/**
	 * @var string
	 */
	public $lastNameStartsWith;
}

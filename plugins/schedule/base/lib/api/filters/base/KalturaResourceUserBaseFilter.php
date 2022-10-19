<?php
/**
 * @package api
 * @relatedService ResourceUserService
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaResourceUserBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"resourceTagEqual" => "_eq_resource_tag",
		"resourceTagIn" => "_in_resource_tag",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
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
	public $resourceTagEqual;

	/**
	 * @var string
	 */
	public $resourceTagIn;

	/**
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var KalturaResourceUserStatus
	 */
	public $statusEqual;

	/**
	 * @dynamicType KalturaResourceUserStatus
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;
}

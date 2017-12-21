<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVendorProfileBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"profileTypeEqual" => "_eq_profile_type",
		"profileTypeIn" => "_in_profile_type",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
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
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

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

	/**
	 * @var KalturaVendorProfileStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaVendorProfileType
	 */
	public $profileTypeEqual;

	/**
	 * @var string
	 */
	public $profileTypeIn;
}

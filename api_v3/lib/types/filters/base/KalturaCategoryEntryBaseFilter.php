<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaCategoryEntryBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"categoryIdEqual" => "_eq_category_id",
		"categoryIdIn" => "_in_category_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"categoryFullIdsStartsWith" => "_likex_category_full_ids",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
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
	public $categoryIdEqual;

	/**
	 * @var string
	 */
	public $categoryIdIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $categoryFullIdsStartsWith;

	/**
	 * @var KalturaCategoryEntryStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;
}

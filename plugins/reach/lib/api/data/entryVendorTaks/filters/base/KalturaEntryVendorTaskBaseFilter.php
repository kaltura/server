<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaEntryVendorTaskBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"vendorPartnerIdEqual" => "_eq_vendor_partner_id",
		"vendorPartnerIdIn" => "_in_vendor_partner_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"queueTimeGreaterThanOrEqual" => "_gte_queue_time",
		"queueTimeLessThanOrEqual" => "_lte_queue_time",
		"finishTimeGreaterThanOrEqual" => "_gte_finish_time",
		"finishTimeLessThanOrEqual" => "_lte_finish_time",
		"entryIdEqual" => "_eq_entry_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"vendorProfileIdEqual" => "_eq_vendor_profile_id",
		"vendorProfileIdIn" => "_in_vendor_profile_id",
		"catalogItemIdEqual" => "_eq_catalog_item_id",
		"catalogItemIdIn" => "_in_catalog_item_id",
		"userIdEqual" => "_eq_user_id",
		"contextEqual" => "_eq_context",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+queueTime" => "+queue_time",
		"-queueTime" => "-queue_time",
		"+finishTime" => "+finish_time",
		"-finishTime" => "-finish_time",
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
	 * @var int
	 */
	public $vendorPartnerIdEqual;

	/**
	 * @var string
	 */
	public $vendorPartnerIdIn;

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
	 * @var time
	 */
	public $queueTimeGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $queueTimeLessThanOrEqual;

	/**
	 * @var time
	 */
	public $finishTimeGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $finishTimeLessThanOrEqual;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var KalturaEntryVendorTaskStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var int
	 */
	public $vendorProfileIdEqual;

	/**
	 * @var string
	 */
	public $vendorProfileIdIn;

	/**
	 * @var int
	 */
	public $catalogItemIdEqual;

	/**
	 * @var string
	 */
	public $catalogItemIdIn;

	/**
	 * @var string
	 */
	public $userIdEqual;
	
	/**
	 * @var string
	 */
	public $contextEqual;
}

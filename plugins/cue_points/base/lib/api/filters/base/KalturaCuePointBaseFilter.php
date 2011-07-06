<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaCuePointBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"startTimeGreaterThanOrEqual" => "_gte_start_time",
		"startTimeLessThanOrEqual" => "_lte_start_time",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"partnerSortValueEqual" => "_eq_partner_sort_value",
		"partnerSortValueIn" => "_in_partner_sort_value",
		"partnerSortValueGreaterThanOrEqual" => "_gte_partner_sort_value",
		"partnerSortValueLessThanOrEqual" => "_lte_partner_sort_value",
		"forceStopEqual" => "_eq_force_stop",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+startTime" => "+start_time",
		"-startTime" => "-start_time",
		"+partnerSortValue" => "+partner_sort_value",
		"-partnerSortValue" => "-partner_sort_value",
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
	 * @var string
	 */
	public $idEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $idIn;

	/**
	 * 
	 * 
	 * @var KalturaCuePointType
	 */
	public $typeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaCuePointType
	 * @var string
	 */
	public $typeIn;

	/**
	 * 
	 * 
	 * @var KalturaCuePointStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startTimeLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $userIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerSortValueEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $partnerSortValueIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var KalturaNullableBoolean
	 */
	public $forceStopEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $systemNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $systemNameIn;
}

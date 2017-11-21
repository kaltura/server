<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVendorCatalogItemBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"vendorPartnerIdEqual" => "_eq_vendor_partner_id",
		"vendorPartnerIdIn" => "_in_vendor_partner_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"isDefaultEqual" => "_eq_is_default",
		"serviceTypeEqual" => "_eq_service_type",
		"serviceTypeIn" => "_in_service_type",
		"turnAroundTimeEqual" => "_eq_turn_around_time",
		"turnAroundTimeIn" => "_in_turn_around_time",
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
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

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
	 * @var KalturaVendorCatalogItemStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isDefaultEqual;

	/**
	 * @var KalturaVendorServiceType
	 */
	public $serviceTypeEqual;

	/**
	 * @var string
	 */
	public $serviceTypeIn;

	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTimeEqual;

	/**
	 * @var string
	 */
	public $turnAroundTimeIn;
}

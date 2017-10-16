<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserEntryBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"entryIdNotIn" => "_notin_entry_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"userIdNotIn" => "_notin_user_id",
		"statusEqual" => "_eq_status",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"typeEqual" => "_eq_type",
		"extendedStatusEqual" => "_eq_extended_status",
		"extendedStatusIn" => "_in_extended_status",
		"extendedStatusNotIn" => "_notin_extended_status",
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
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $idNotIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var string
	 */
	public $entryIdNotIn;

	/**
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var string
	 */
	public $userIdNotIn;

	/**
	 * @var KalturaUserEntryStatus
	 */
	public $statusEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var KalturaUserEntryType
	 */
	public $typeEqual;

	/**
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatusEqual;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusIn;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusNotIn;
}

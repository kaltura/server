<?php
/**
 * @package plugins.fileSync
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaFileSyncBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"fileObjectTypeEqual" => "_eq_file_object_type",
		"fileObjectTypeIn" => "_in_file_object_type",
		"objectIdEqual" => "_eq_object_id",
		"objectIdIn" => "_in_object_id",
		"versionEqual" => "_eq_version",
		"versionIn" => "_in_version",
		"objectSubTypeEqual" => "_eq_object_sub_type",
		"objectSubTypeIn" => "_in_object_sub_type",
		"dcEqual" => "_eq_dc",
		"dcIn" => "_in_dc",
		"originalEqual" => "_eq_original",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"readyAtGreaterThanOrEqual" => "_gte_ready_at",
		"readyAtLessThanOrEqual" => "_lte_ready_at",
		"syncTimeGreaterThanOrEqual" => "_gte_sync_time",
		"syncTimeLessThanOrEqual" => "_lte_sync_time",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"fileTypeEqual" => "_eq_file_type",
		"fileTypeIn" => "_in_file_type",
		"linkedIdEqual" => "_eq_linked_id",
		"linkCountGreaterThanOrEqual" => "_gte_link_count",
		"linkCountLessThanOrEqual" => "_lte_link_count",
		"fileSizeGreaterThanOrEqual" => "_gte_file_size",
		"fileSizeLessThanOrEqual" => "_lte_file_size",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+readyAt" => "+ready_at",
		"-readyAt" => "-ready_at",
		"+syncTime" => "+sync_time",
		"-syncTime" => "-sync_time",
		"+fileSize" => "+file_size",
		"-fileSize" => "-file_size",
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
	public $partnerIdEqual;

	/**
	 * @var KalturaFileSyncObjectType
	 */
	public $fileObjectTypeEqual;

	/**
	 * @dynamicType KalturaFileSyncObjectType
	 * @var string
	 */
	public $fileObjectTypeIn;

	/**
	 * @var string
	 */
	public $objectIdEqual;

	/**
	 * @var string
	 */
	public $objectIdIn;

	/**
	 * @var string
	 */
	public $versionEqual;

	/**
	 * @var string
	 */
	public $versionIn;

	/**
	 * @var int
	 */
	public $objectSubTypeEqual;

	/**
	 * @var string
	 */
	public $objectSubTypeIn;

	/**
	 * @var string
	 */
	public $dcEqual;

	/**
	 * @var string
	 */
	public $dcIn;

	/**
	 * @var int
	 */
	public $originalEqual;

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
	 * @var int
	 */
	public $readyAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $readyAtLessThanOrEqual;

	/**
	 * @var int
	 */
	public $syncTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $syncTimeLessThanOrEqual;

	/**
	 * @var KalturaFileSyncStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaFileSyncType
	 */
	public $fileTypeEqual;

	/**
	 * @var string
	 */
	public $fileTypeIn;

	/**
	 * @var int
	 */
	public $linkedIdEqual;

	/**
	 * @var int
	 */
	public $linkCountGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $linkCountLessThanOrEqual;

	/**
	 * @var float
	 */
	public $fileSizeGreaterThanOrEqual;

	/**
	 * @var float
	 */
	public $fileSizeLessThanOrEqual;
}

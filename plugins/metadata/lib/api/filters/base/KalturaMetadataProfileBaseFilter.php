<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaMetadataProfileBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"partnerIdEqual" => "_eq_partner_id",
		"metadataObjectTypeEqual" => "_eq_metadata_object_type",
		"metadataObjectTypeIn" => "_in_metadata_object_type",
		"versionEqual" => "_eq_version",
		"nameEqual" => "_eq_name",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createModeEqual" => "_eq_create_mode",
		"createModeNotEqual" => "_not_create_mode",
		"createModeIn" => "_in_create_mode",
		"createModeNotIn" => "_notin_create_mode",
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
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual;

	/**
	 * @dynamicType KalturaMetadataObjectType
	 * @var string
	 */
	public $metadataObjectTypeIn;

	/**
	 * @var int
	 */
	public $versionEqual;

	/**
	 * @var string
	 */
	public $nameEqual;

	/**
	 * @var string
	 */
	public $systemNameEqual;

	/**
	 * @var string
	 */
	public $systemNameIn;

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
	 * @var KalturaMetadataProfileStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var KalturaMetadataProfileCreateMode
	 */
	public $createModeEqual;

	/**
	 * @var KalturaMetadataProfileCreateMode
	 */
	public $createModeNotEqual;

	/**
	 * @var string
	 */
	public $createModeIn;

	/**
	 * @var string
	 */
	public $createModeNotIn;
}

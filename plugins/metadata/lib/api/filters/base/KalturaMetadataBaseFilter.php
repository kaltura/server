<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaMetadataBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"metadataProfileIdEqual" => "_eq_metadata_profile_id",
		"metadataProfileIdIn" => "_in_metadata_profile_id",
		"metadataProfileVersionEqual" => "_eq_metadata_profile_version",
		"metadataProfileVersionGreaterThanOrEqual" => "_gte_metadata_profile_version",
		"metadataProfileVersionLessThanOrEqual" => "_lte_metadata_profile_version",
		"metadataObjectTypeEqual" => "_eq_metadata_object_type",
		"objectIdEqual" => "_eq_object_id",
		"objectIdIn" => "_in_object_id",
		"versionEqual" => "_eq_version",
		"versionGreaterThanOrEqual" => "_gte_version",
		"versionLessThanOrEqual" => "_lte_version",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	static private $order_by_map = array
	(
		"+metadataProfileVersion" => "+metadata_profile_version",
		"-metadataProfileVersion" => "-metadata_profile_version",
		"+version" => "+version",
		"-version" => "-version",
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
	public $partnerIdEqual;

	/**
	 * @var int
	 */
	public $metadataProfileIdEqual;

	/**
	 * @var string
	 */
	public $metadataProfileIdIn;

	/**
	 * @var int
	 */
	public $metadataProfileVersionEqual;

	/**
	 * @var int
	 */
	public $metadataProfileVersionGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $metadataProfileVersionLessThanOrEqual;

	/**
	 * When null, default is KalturaMetadataObjectType::ENTRY
	 * 
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual;

	/**
	 * @var string
	 */
	public $objectIdEqual;

	/**
	 * @var string
	 */
	public $objectIdIn;

	/**
	 * @var int
	 */
	public $versionEqual;

	/**
	 * @var int
	 */
	public $versionGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $versionLessThanOrEqual;

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
	 * @var KalturaMetadataStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;
}

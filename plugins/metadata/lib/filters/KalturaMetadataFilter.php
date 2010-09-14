<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMetadataFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"metadataProfileIdEqual" => "_eq_metadata_profile_id",
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

	private $order_by_map = array
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
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $metadataProfileIdEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $metadataProfileVersionEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $metadataProfileVersionGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $metadataProfileVersionLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $objectIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $objectIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $versionEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $versionGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $versionLessThanOrEqual;

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
	 * @var KalturaMetadataStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;
}

<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaMetadataProfileBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"partnerIdEqual" => "_eq_partner_id",
		"metadataObjectTypeEqual" => "_eq_metadata_object_type",
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
	);

	private $order_by_map = array
	(
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
	public $idEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $versionEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameEqual;

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
	 * @var KalturaMetadataProfileStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;
}

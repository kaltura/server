<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaAssetBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
		"sizeGreaterThanOrEqual" => "_gte_size",
		"sizeLessThanOrEqual" => "_lte_size",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"deletedAtGreaterThanOrEqual" => "_gte_deleted_at",
		"deletedAtLessThanOrEqual" => "_lte_deleted_at",
	);

	private $order_by_map = array
	(
		"+size" => "+size",
		"-size" => "-size",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+deletedAt" => "+deleted_at",
		"-deletedAt" => "-deleted_at",
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
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * 
	 * 
	 * @var KalturaFlavorAssetStatus
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
	public $statusNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sizeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sizeLessThanOrEqual;

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
	public $deletedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $deletedAtLessThanOrEqual;
}

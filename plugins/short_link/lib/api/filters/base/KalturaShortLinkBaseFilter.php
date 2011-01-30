<?php
/**
 * @package plugins.shortLink
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaShortLinkBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"expiresAtGreaterThanOrEqual" => "_gte_expires_at",
		"expiresAtLessThanOrEqual" => "_lte_expires_at",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+expiresAt" => "+expires_at",
		"-expiresAt" => "-expires_at",
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
	 * @var string
	 */
	public $idIn;

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
	public $expiresAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $expiresAtLessThanOrEqual;

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
	 * @var KalturaShortLinkStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;
}

<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaUserRoleBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"nameEqual" => "_eq_name",
		"nameIn" => "_in_name",
		"descriptionLike" => "_like_description",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"permissionNamesMultiLikeOr" => "_mlikeor_permission_names",
		"permissionNamesMultiLikeAnd" => "_mlikeand_permission_names",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+name" => "+name",
		"-name" => "-name",
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
	 * @var string
	 */
	public $idIn;

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
	public $nameIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $descriptionLike;

	/**
	 * 
	 * 
	 * @var KalturaUserRoleStatus
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
	public $permissionNamesMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $permissionNamesMultiLikeAnd;

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
}

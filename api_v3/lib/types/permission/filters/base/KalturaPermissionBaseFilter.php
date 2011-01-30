<?php
/**
 * @package api
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaPermissionBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"nameEqual" => "_eq_name",
		"nameIn" => "_in_name",
		"friendlyNameLike" => "_like_friendly_name",
		"descriptionLike" => "_like_description",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"dependsOnPermissionNamesMultiLikeOr" => "_mlikeor_depends_on_permission_names",
		"dependsOnPermissionNamesMultiLikeAnd" => "_mlikeand_depends_on_permission_names",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
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
	 * @var KalturaPermissionType
	 */
	public $typeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $typeIn;

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
	public $friendlyNameLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $descriptionLike;

	/**
	 * 
	 * 
	 * @var KalturaPermissionStatus
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
	public $dependsOnPermissionNamesMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dependsOnPermissionNamesMultiLikeAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagsMultiLikeAnd;

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

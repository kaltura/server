<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaCategoryBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"depthEqual" => "_eq_depth",
		"fullNameEqual" => "_eq_full_name",
		"fullNameStartsWith" => "_likex_full_name",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"appearInListEqual" => "_eq_appear_in_list",
		"privacyEqual" => "_eq_privacy",
		"privacyIn" => "_in_privacy",
		"inheritanceTypeEqual" => "_eq_inheritance_type",
		"inheritanceTypeIn" => "_in_inheritance_type",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	private $order_by_map = array
	(
		"+depth" => "+depth",
		"-depth" => "-depth",
		"+fullName" => "+full_name",
		"-fullName" => "-full_name",
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
	 * @var int
	 */
	public $parentIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $depthEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fullNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fullNameStartsWith;

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
	 * @var string
	 */
	public $tagsLike;

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
	 * @var KalturaAppearInListType
	 */
	public $appearInListEqual;

	/**
	 * 
	 * 
	 * @var KalturaPrivacyType
	 */
	public $privacyEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $privacyIn;

	/**
	 * 
	 * 
	 * @var KalturaInheritanceType
	 */
	public $inheritanceTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $inheritanceTypeIn;

	/**
	 * 
	 * 
	 * @var KalturaCategoryStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;
}

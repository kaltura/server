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
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"listingEqual" => "_eq_listing",
		"privacyEqual" => "_eq_privacy",
		"privacyIn" => "_in_privacy",
		"membershipSettingEqual" => "_eq_membership_setting",
		"membershipSettingIn" => "_in_membership_setting",
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
	 * @var KalturaListingType
	 */
	public $listingEqual;

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
	 * @var KalturaCategoryMembershipSettingType
	 */
	public $membershipSettingEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $membershipSettingIn;

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

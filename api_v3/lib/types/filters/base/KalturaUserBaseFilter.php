<?php
/**
 * @package api
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaUserBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"screenNameLike" => "_like_screen_name",
		"screenNameStartsWith" => "_likex_screen_name",
		"emailLike" => "_like_email",
		"emailStartsWith" => "_likex_email",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"isAdminEqual" => "_eq_is_admin",
	);

	private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
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
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $screenNameLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $screenNameStartsWith;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $emailLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $emailStartsWith;

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
	 * @var KalturaUserStatus
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
	 * @var bool
	 */
	public $isAdminEqual;
}

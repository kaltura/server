<?php
/**
 * @package api
 * @relatedService UserService
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
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
		"firstNameStartsWith" => "_likex_first_name",
		"lastNameStartsWith" => "_likex_last_name",
		"isAdminEqual" => "_eq_is_admin",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
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
	 * @var KalturaUserType
	 */
	public $typeEqual;

	/**
	 * @var string
	 */
	public $typeIn;

	/**
	 * @var string
	 */
	public $screenNameLike;

	/**
	 * @var string
	 */
	public $screenNameStartsWith;

	/**
	 * @var string
	 */
	public $emailLike;

	/**
	 * @var string
	 */
	public $emailStartsWith;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * @var KalturaUserStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $firstNameStartsWith;

	/**
	 * @var string
	 */
	public $lastNameStartsWith;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAdminEqual;
}

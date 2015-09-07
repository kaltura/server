<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaPartnerBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"nameLike" => "_like_name",
		"nameMultiLikeOr" => "_mlikeor_name",
		"nameMultiLikeAnd" => "_mlikeand_name",
		"nameEqual" => "_eq_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"partnerPackageEqual" => "_eq_partner_package",
		"partnerPackageGreaterThanOrEqual" => "_gte_partner_package",
		"partnerPackageLessThanOrEqual" => "_lte_partner_package",
		"partnerPackageIn" => "_in_partner_package",
		"partnerGroupTypeEqual" => "_eq_partner_group_type",
		"partnerNameDescriptionWebsiteAdminNameAdminEmailLike" => "_like_partner_name-description-website-admin_name-admin_email",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+name" => "+name",
		"-name" => "-name",
		"+website" => "+website",
		"-website" => "-website",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+adminName" => "+admin_name",
		"-adminName" => "-admin_name",
		"+adminEmail" => "+admin_email",
		"-adminEmail" => "-admin_email",
		"+status" => "+status",
		"-status" => "-status",
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
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $idNotIn;

	/**
	 * @var string
	 */
	public $nameLike;

	/**
	 * @var string
	 */
	public $nameMultiLikeOr;

	/**
	 * @var string
	 */
	public $nameMultiLikeAnd;

	/**
	 * @var string
	 */
	public $nameEqual;

	/**
	 * @var KalturaPartnerStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var int
	 */
	public $partnerPackageEqual;

	/**
	 * @var int
	 */
	public $partnerPackageGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $partnerPackageLessThanOrEqual;

	/**
	 * @var string
	 */
	public $partnerPackageIn;

	/**
	 * @var KalturaPartnerGroupType
	 */
	public $partnerGroupTypeEqual;

	/**
	 * @var string
	 */
	public $partnerNameDescriptionWebsiteAdminNameAdminEmailLike;
}

<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaPartnerBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"nameLike" => "_like_name",
		"nameMultiLikeOr" => "_mlikeor_name",
		"nameMultiLikeAnd" => "_mlikeand_name",
		"nameEqual" => "_eq_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"partnerNameDescriptionWebsiteAdminNameAdminEmailLike" => "_like_partner_name-description-website-admin_name-admin_email",
	);

	private $order_by_map = array
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
	public $nameLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameMultiLikeAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $partnerNameDescriptionWebsiteAdminNameAdminEmailLike;
}

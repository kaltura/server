<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaUserBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"screenNameLike" => "_like_screen_name",
		"screenNameStartsWith" => "_likex_screen_name",
		"emailLike" => "_like_email",
		"emailStartsWith" => "_likex_email",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
	);

	private $order_by_map = array
	(
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
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtLessThanOrEqual;
}

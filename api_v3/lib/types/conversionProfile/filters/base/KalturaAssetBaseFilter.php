<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaAssetBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"sizeGreaterThanOrEqual" => "_gte_size",
		"sizeLessThanOrEqual" => "_lte_size",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"deletedAtGreaterThanOrEqual" => "_gte_deleted_at",
		"deletedAtLessThanOrEqual" => "_lte_deleted_at",
	);

	static private $order_by_map = array
	(
		"+size" => "+size",
		"-size" => "-size",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+deletedAt" => "+deleted_at",
		"-deletedAt" => "-deleted_at",
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
	 * @var string
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * @var int
	 */
	public $sizeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $sizeLessThanOrEqual;

	/**
	 * @var string
	 */
	public $tagsLike;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $deletedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $deletedAtLessThanOrEqual;
}

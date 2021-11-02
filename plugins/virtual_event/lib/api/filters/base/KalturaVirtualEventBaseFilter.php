<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVirtualEventBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"nameLike" => "_like_name",
		"nameMultiLikeOr" => "_mlikeor_name",
		"nameMultiLikeAnd" => "_mlikeand_name",
		"nameEqual" => "_eq_name",
		"descriptionLike" => "_like_description",
		"descriptionMultiLikeOr" => "_mlikeor_description",
		"descriptionMultiLikeAnd" => "_mlikeand_description",
		"descriptionEqual" => "_eq_description",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"tagsEqual" => "_eq_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"deletionDueDateGreaterThanOrEqual" => "_gte_deletion_due_date",
		"deletionDueDateLessThanOrEqual" => "_lte_deletion_due_date",
	);

	static private $order_by_map = array
	(
		"+name" => "+name",
		"-name" => "-name",
		"+description" => "+description",
		"-description" => "-description",
		"+tags" => "+tags",
		"-tags" => "-tags",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+deletionDueDate" => "+deletion_due_date",
		"-deletionDueDate" => "-deletion_due_date",
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
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

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
	 * @var string
	 */
	public $descriptionLike;

	/**
	 * @var string
	 */
	public $descriptionMultiLikeOr;

	/**
	 * @var string
	 */
	public $descriptionMultiLikeAnd;

	/**
	 * @var string
	 */
	public $descriptionEqual;

	/**
	 * @var KalturaVirtualEventStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

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
	 * @var string
	 */
	public $tagsEqual;

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
	public $deletionDueDateGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $deletionDueDateLessThanOrEqual;
}

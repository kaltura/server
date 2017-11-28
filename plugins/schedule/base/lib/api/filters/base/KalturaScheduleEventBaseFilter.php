<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaScheduleEventBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"parentIdNotIn" => "_notin_parent_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"startDateGreaterThanOrEqual" => "_gte_start_date",
		"startDateLessThanOrEqual" => "_lte_start_date",
		"endDateGreaterThanOrEqual" => "_gte_end_date",
		"endDateLessThanOrEqual" => "_lte_end_date",
		"referenceIdEqual" => "_eq_reference_id",
		"referenceIdIn" => "_in_reference_id",
		"ownerIdEqual" => "_eq_owner_id",
		"ownerIdIn" => "_in_owner_id",
		"priorityEqual" => "_eq_priority",
		"priorityIn" => "_in_priority",
		"priorityGreaterThanOrEqual" => "_gte_priority",
		"priorityLessThanOrEqual" => "_lte_priority",
		"recurrenceTypeEqual" => "_eq_recurrence_type",
		"recurrenceTypeIn" => "_in_recurrence_type",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	static private $order_by_map = array
	(
		"+summary" => "+summary",
		"-summary" => "-summary",
		"+startDate" => "+start_date",
		"-startDate" => "-start_date",
		"+endDate" => "+end_date",
		"-endDate" => "-end_date",
		"+priority" => "+priority",
		"-priority" => "-priority",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
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
	public $parentIdEqual;

	/**
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * @var string
	 */
	public $parentIdNotIn;

	/**
	 * @var KalturaScheduleEventStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var time
	 */
	public $startDateGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $startDateLessThanOrEqual;

	/**
	 * @var time
	 */
	public $endDateGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $endDateLessThanOrEqual;

	/**
	 * @var string
	 */
	public $referenceIdEqual;

	/**
	 * @var string
	 */
	public $referenceIdIn;

	/**
	 * @var string
	 */
	public $ownerIdEqual;

	/**
	 * @var string
	 */
	public $ownerIdIn;

	/**
	 * @var int
	 */
	public $priorityEqual;

	/**
	 * @var string
	 */
	public $priorityIn;

	/**
	 * @var int
	 */
	public $priorityGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $priorityLessThanOrEqual;

	/**
	 * @var KalturaScheduleEventRecurrenceType
	 */
	public $recurrenceTypeEqual;

	/**
	 * @var string
	 */
	public $recurrenceTypeIn;

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
}

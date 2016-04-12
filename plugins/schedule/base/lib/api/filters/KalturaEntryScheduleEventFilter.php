<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaEntryScheduleEventFilter extends KalturaEntryScheduleEventBaseFilter
{
	static private $map_between_objects = array
	(
		"parentCategoryIdsLike" => "_like_parent_category_ids",
		"parentCategoryIdsMultiLikeOr" => "_mlikeor_parent_category_ids",
		"parentCategoryIdsMultiLikeAnd" => "_mlikeand_parent_category_ids",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @var string
	 */
	public $parentCategoryIdsLike;

	/**
	 * @var string
	 */
	public $parentCategoryIdsMultiLikeOr;

	/**
	 * @var string
	 */
	public $parentCategoryIdsMultiLikeAnd;
}

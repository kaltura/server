<?php
/**
 * @package plugins.rating
 * @relatedService RatingService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaRatingCountBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"entryIdEqual" => "_eq_entry_id",
		"rankEqual" => "_eq_rank",
		"rankIn" => "_in_rank",
	);

	static private $order_by_map = array
	(
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
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $rankIn;
}

<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaBeaconBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"relatedObjectTypeEqual" => "_eq_related_object_type",
		"eventTypeEqual" => "_eq_event_type",
		"objectIdEqual" => "_eq_object_id",
		"privateDataLike" => "_like_private_data",
		"privateDataMultiLikeOr" => "_mlikeor_private_data",
		"privateDataMultiLikeAnd" => "_mlikeand_private_data",
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
	 * @var KalturaBeaconObjectTypes
	 */
	public $relatedObjectTypeEqual;

	/**
	 * @var string
	 */
	public $eventTypeEqual;

	/**
	 * @var string
	 */
	public $objectIdEqual;

	/**
	 * @var string
	 */
	public $privateDataLike;

	/**
	 * @var string
	 */
	public $privateDataMultiLikeOr;

	/**
	 * @var string
	 */
	public $privateDataMultiLikeAnd;
}

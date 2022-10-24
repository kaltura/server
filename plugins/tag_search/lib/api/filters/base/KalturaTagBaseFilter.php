<?php
/**
 * @package plugins.tagSearch
 * @relatedService TagService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaTagBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"tagEqual" => "_eq_tag",
		"tagStartsWith" => "_likex_tag",
		"taggedObjectTypeEqual" => "_eq_tagged_object_type",
		"instanceCountEqual" => "_eq_instance_count",
		"instanceCountIn" => "_in_instance_count",
		"instanceCountGreaterThanOrEqual" => "_gte_instance_count",
		"instanceCountLessThanOrEqual" => "_lte_instance_count",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
	);

	static private $order_by_map = array
	(
		"+instanceCount" => "+instance_count",
		"-instanceCount" => "-instance_count",
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
	 * @var string
	 */
	public $tagEqual;

	/**
	 * @var string
	 */
	public $tagStartsWith;

	/**
	 * @var KalturaTaggedObjectType
	 */
	public $taggedObjectTypeEqual;

	/**
	 * @var int
	 */
	public $instanceCountEqual;

	/**
	 * @var string
	 */
	public $instanceCountIn;

	/**
	 * @var int
	 */
	public $instanceCountGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $instanceCountLessThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;
}

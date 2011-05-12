<?php
/**
 * @package 
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaCategoryBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"depthEqual" => "_eq_depth",
		"fullNameEqual" => "_eq_full_name",
		"fullNameStartsWith" => "_likex_full_name",
	);

	private $order_by_map = array
	(
		"+depth" => "+depth",
		"-depth" => "-depth",
		"+fullName" => "+full_name",
		"-fullName" => "-full_name",
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
	 * @var int
	 */
	public $parentIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $depthEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fullNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fullNameStartsWith;
}

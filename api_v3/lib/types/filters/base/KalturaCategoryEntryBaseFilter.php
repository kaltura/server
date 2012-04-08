<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaCategoryEntryBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"categoryIdEqual" => "_eq_category_id",
		"categoryIdIn" => "_in_category_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
	);

	private $order_by_map = array
	(
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
	public $categoryIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $categoryIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdIn;
}

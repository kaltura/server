<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryFilter extends KalturaCategoryBaseFilter
{
	private $map_between_objects = array
	(
		"freeText" => "_free_text",
		"membersIn" => "_in_members",
		"appearInListEqual" => "_eq_display_in_search",
	);

	private $order_by_map = array
	(
		"+fullName" => "+full_name",
		"-fullName" => "-full_name",
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
	 * @var string
	 */
	public $freeText;

	/**
	 * @var string
	 */
	public $membersIn;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new categoryFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
}

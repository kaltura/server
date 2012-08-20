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
		"nameOrReferenceIdStartsWith" => "_likex_name_or_reference_id",
		"managerEqual" => "_eq_manager",
		"memberEqual" => "_eq_member",
		"fullNameStartsWithIn" => '_matchor_likex_full_name',
		"ancestorIdIn" => "_in_ancestor_id",
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

	/**
	 * @var string
	 */
	public $nameOrReferenceIdStartsWith;
	
	/**
	 * @var string
	 */
	public $managerEqual;
	
	/**
	 * @var string
	 */
	public $memberEqual;
	
	/**
	 * @var string
	 */
	public $fullNameStartsWithIn;
		
	/**
	 * not includes the category itself (only sub categories)
	 * @var string
	 */
	public $ancestorIdIn;
	
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

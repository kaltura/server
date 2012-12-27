<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBaseEntryFilter extends KalturaBaseEntryBaseFilter
{
	private $map_between_objects = array
	(
		"freeText" => "_free_text",
		"isRoot" => "_is_root",
		"categoriesFullNameIn" => "_in_categories_full_name", 
		"categoryAncestorIdIn" => "_in_category_ancestor_id",
	);
	
	private $order_by_map = array
	(
		"recent" => "recent", // needed for backward compatibility
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
	 * @var KalturaNullableBoolean
	 */
	public $isRoot;
	
	/**
	 * @var string
	 */
	public $categoriesFullNameIn;
	
	/**
	 * All entries within this categoy or in child categories  
	 * @var string
	 */
	public $categoryAncestorIdIn;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new entryFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
}

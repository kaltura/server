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

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
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

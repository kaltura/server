<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryFilter extends KalturaCategoryBaseFilter
{
	private $map_between_objects = array
	(
		"searchText" => "_search_text",
		"membersIn" => "_in_members",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * @var string
	 */
	public $searchText;

	/**
	 * @var string
	 */
	public $membersIn;
	
}

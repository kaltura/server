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
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * @var string
	 */
	public $freeText;
}

<?php

class KalturaDataEntry extends KalturaBaseEntry
{
	/**
	 * The data of the entry
	 *
	 * @var string
	 */
	public $dataContent;
	
	private static $map_between_objects = array(
		"dataContent",
	);
	
	public function __construct()
	{
		$this->type = KalturaEntryType::DATA;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}

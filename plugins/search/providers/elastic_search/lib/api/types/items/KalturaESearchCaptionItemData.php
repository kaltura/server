<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
//TODO: should class be moved to caption plugin?
class KalturaESearchCaptionItemData extends KalturaESearchItemData {

	/**
	 * @var string
	 */
	public $line;

	/**
	 * @var int
	 */
	public $startsAt;

	/**
	 * @var int
	 */
	public $endsAt;

	private static $map_between_objects = array(
		'line',
		'startsAt',
		'endsAt',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCaptionItemData();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}

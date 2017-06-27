<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCaptionItem extends KalturaESearchItem {

	/**
	 * @var int
	 */
	public $startTimeInVideo;

	/**
	 * @var int
	 */
	public $endTimeInVideo;

	private static $map_between_objects = array(
		'startTimeInVideo',
		'endTimeInVideo',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCaptionItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}

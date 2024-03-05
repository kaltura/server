<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamInfo extends KalturaObject
{
	/**
	 * @var int
	 */
	public $liveViewers = 0;

	private static $map_between_objects = array
	(
	);
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}

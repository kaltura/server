<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNodeRecordingInfo extends KalturaObject {
	
	/**
	 * @var string
	 */
	public $recordedEntryId;
	
	/**
	 * @var int
	 */
	public $duration;
	
	private static $mapBetweenObjects = array
	(
			"recordedEntryId",
			"duration",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	*/
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new LiveEntryServerNodeRecordingInfo();
		}
	
		return parent::toObject($dbObject, $propsToSkip);
	}
}
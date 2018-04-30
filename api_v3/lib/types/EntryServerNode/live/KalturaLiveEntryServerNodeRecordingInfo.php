<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNodeRecordingInfo extends KalturaObject
{
	
	/**
	 * @var string
	 */
	public $recordedEntryId;
	
	/**
	 * @var int
	 */
	public $duration;

	/**
	 * @var KalturaEntryServerNodeRecordingStatus
	 */
	public $recordingStatus;
	
	private static $mapBetweenObjects = array
	(
			"recordedEntryId",
			"duration",
			"recordingStatus",
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
		if (is_null($this->recordingStatus))
			$this->recordingStatus = KalturaEntryServerNodeRecordingStatus::STOPPED;
	
		return parent::toObject($dbObject, $propsToSkip);
	}
}
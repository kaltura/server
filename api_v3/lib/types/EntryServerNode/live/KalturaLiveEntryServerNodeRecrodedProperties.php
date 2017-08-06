<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNodeRecrodedProperties extends KalturaObject
{

	/**
	 * @var int
	 */
	public $duration;

	/**
	 * @readonly
	 * @var KalturaKeyValueArray
	 */
	public $recordedEntriesDurations;

	private static $map_between_objects = array
	(
		"duration",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new LiveEntryServerNodeRecordedProperties();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}


	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
		$outArr = array();
		foreach($srcObj->getRecordedEntriesDurations() as $recordedEntryDuration)
		{
			$outArr[$recordedEntryDuration['entryId']] = $recordedEntryDuration['duration'];
		}
		$this->recordedEntriesDurations = KalturaKeyValueArray::fromDbArray($outArr);
	}


}
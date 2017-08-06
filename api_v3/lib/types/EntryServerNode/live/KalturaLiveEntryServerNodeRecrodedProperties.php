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
		"recordedEntriesDurations",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
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
<?php
/**
 * A representation of a live stream recording entry configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryRecordingOptions extends KalturaObject
{
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyEntitlement;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyScheduling;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyThumbnail;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $shouldMakeHidden;

    /**
     * @var KalturaNullableBoolean
     */
	public $shouldAutoArchive;

    /**
     * @var string
     */
	public $nonDeletedCuePointsTags;

	/**
	 * @var string
	 */
	public $archiveVodSuffixTimezone;

	private static $mapBetweenObjects = array
	(
		"shouldCopyEntitlement",
		"shouldCopyScheduling",
		"shouldCopyThumbnail",
		"shouldMakeHidden",
		"shouldAutoArchive",
		"nonDeletedCuePointsTags",
		"archiveVodSuffixTimezone",
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
			$dbObject = new kLiveEntryRecordingOptions();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}

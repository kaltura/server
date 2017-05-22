<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamParams extends KalturaObject {
	
	/**
	 * Bit rate of the stream. (i.e. 900)
	 * @var int
	 */
	public $bitrate;
	
	/**
	 * flavor asset id
	 * @var string
	 */
	public $flavorId;
	
	/**
	 * Stream's width
	 * @var int
	 */
	public $width;
	
	/**
	 * Stream's height
	 * @var int
	 */
	public $height;
	
	/**
	 * Live stream's codec
	 * @var string
	 */
	public $codec;
	
	/**
	 * Live stream's farme rate
	 * @var int
	 */
	public $frameRate;
	
	/**
	 * Live stream's key frame interval
	 * @var float
	 */
	public $keyFrameInterval;
	
	/**
	 * Live stream's language  
	 * @var string
	 */
	public $language;
	
	private static $mapBetweenObjects = array
	(
			"bitrate",
			"flavorId",
			"width",
			"height",
			"codec",
			"frameRate",
			"keyFrameInterval",
			"language"
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
			$dbObject = new kLiveStreamParams();
		}
	
		return parent::toObject($dbObject, $propsToSkip);
	}
}
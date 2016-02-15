<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNode extends KalturaEntryServerNode {

	/**
	 * Bit rate of the stream. (i.e. 900)
	 * @var string
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
	 * // TODO what is this field about
	 * @var string
	 */
	public $pattern;

	private static $map_between_objects = array
	(
		"bitrate",
		"flavorId",
		"width",
		"height",
		"codec",
		"pattern",
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
			$object_to_fill = new LiveEntryServerNode();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
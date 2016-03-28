<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNode extends KalturaEntryServerNode {

	/**
	 * parameters of the stream we got
	 * @var KalturaLiveStreamParamsArray
	 */
	public $streams;

	private static $map_between_objects = array
	(
		"streams"
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
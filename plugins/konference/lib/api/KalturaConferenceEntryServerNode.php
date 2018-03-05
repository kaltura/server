<?php
/**
 * @package plugins.konference
 * @subpackage api.objects
 */
class KalturaConferenceEntryServerNode extends KalturaEntryServerNode
{

	/**
	 * @var KalturaConferenceRoomStatus
	 * @readonly
	 */
	public $confRoomStatus;

	private static $map_between_objects = array
	(
		"confRoomStatus",
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
			$object_to_fill = new ConferenceEntryServerNode();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}

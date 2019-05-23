<?php
/**
 * @package plugins.sip
 * @subpackage api.objects
 */
class KalturaSipEntryServerNode extends KalturaEntryServerNode
{
	/**
	 * @var string
	 * @readonly
	 */
	public $sipRoomId;

	/**
	 * @var string
	 * @readonly
	 */
	public $sipPrimaryAdpId;

	/**
	 * @var string
	 * @readonly
	 */
	public $sipSecondaryAdpId;

	private static $map_between_objects = array
	(
		'sipRoomId',
		'sipPrimaryAdpId',
		'sipSecondaryAdpId'
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
			$object_to_fill = new SipEntryServerNode();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}

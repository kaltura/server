<?php
/**
 * @package plugins.sip
 * @subpackage api.objects
 */
class KalturaSipServerNode extends KalturaServerNode
{
	private static $map_between_objects = array();

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new SipServerNode();
		}
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
		{
			$dbObject = new SipServerNode();
		}
	
		return parent::toObject($dbObject, $skip);
	}
}

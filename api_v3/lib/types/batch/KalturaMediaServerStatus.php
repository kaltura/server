<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaServerStatus extends KalturaObject
{
	private static $mapBetweenObjects = array
	(
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
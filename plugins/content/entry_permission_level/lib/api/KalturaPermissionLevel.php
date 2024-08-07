<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage api
 */
class KalturaPermissionLevel extends KalturaObject
{
	/**
	 * Permission Level
	 * @var KalturaUserEntryPermissionLevel
	 */
	public $permissionLevel;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array('permissionLevel');
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new PermissionLevel();
		
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}

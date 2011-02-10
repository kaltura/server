<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaApiParameterPermissionItem extends KalturaPermissionItem
{
	
	/**
	 * @var string
	 */
	public $object;
	
	/**
	 * @var string
	 */
	public $parameter;
	
	/**
	 * @var KalturaApiParameterPermissionItemAction
	 */
	public $action;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'object',
		'parameter',
		'action',
	 );
		 
	 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kApiParameterPermissionItem();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
}

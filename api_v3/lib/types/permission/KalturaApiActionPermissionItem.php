<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaApiActionPermissionItem extends KalturaPermissionItem
{
	public $type = 'KalturaApiActionPermissionItem';
	
	/**
	 * @var string
	 */
	public $service;
	
	/**
	 * @var string
	 */
	public $action;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'service',
		'action',
	 );
		 
	 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kApiActionPermissionItem();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}

}
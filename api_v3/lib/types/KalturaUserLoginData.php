<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserLoginData extends KalturaObject implements IRelatedFilterable

{
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $loginEmail;


	private static $map_between_objects = array
	(
		"id", 
		"loginEmail",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new UserLoginData();
			
		return parent::toObject($dbObject, $skip);	
	}
	
	public function getExtraFilters()
	{ 
		return array();		
	}
	
	public function getFilterDocs()
	{
		return array();	
	}
}
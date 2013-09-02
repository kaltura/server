<?php
/**
 * @package plugins.webexDropFolder
 * @subpackage api.objects
 */
class KalturaWebexDropFolder extends KalturaDropFolder
{
	/**
	 * @var string
	 */
	public $webexUserId;
	
	/**
	 * @var string
	 */
	public $webexPassword;
	
	/**
	 * @var int
	 */
	public $webexSiteId;
	
	/**
	 * @var string
	 */	
	public $webexPartnerId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'webexUserId',
		'webexPassword',
		'webexSiteId',
		'webexPartnerId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new WebexDropFolder();
		
		return parent::toObject($dbObject, $skip);
	}
}

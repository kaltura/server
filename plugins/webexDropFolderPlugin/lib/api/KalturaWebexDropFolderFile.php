<?php
/**
 * @package plugins.WebexDropFolder
 * @subpackage api.objects
 */
class KalturaWebexDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * @var int
	 */
	public $recordingId;
	
	/**
	 * @var string
	 */
	public $webexHostId;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $confId;
	
	/**
	 * @var string
	 */
	public $contentUrl;



	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'recordingId',
		'webexHostId',
		'description',
		'confId',
		'contentUrl',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new WebexDropFolderFile();
		
		return parent::toObject($dbObject, $skip);
	}
}

<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */
class KalturaWebexAPIDropFolderFile extends KalturaDropFolderFile
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
	
	/**
	 * @var int
	 */
	public $urlExpiry;
	
	/**
	 * @var string
	 */
	public $fileExtension;
	

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'recordingId',
		'webexHostId',
		'description',
		'confId',
		'contentUrl',
		'urlExpiry',
		'fileExtension',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new WebexAPIDropFolderFile();
		
		return parent::toObject($dbObject, $skip);
	}
}

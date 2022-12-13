<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */
class KalturaWebexAPIDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * @var string
	 */
	public $recordingId;
	
	/**
	 * @var string
	 */
	public $description;
	
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
	
	/**
	 * @var string
	 */
	public $meetingId;
	
	/**
	 * @var int
	 */
	public $recordingStartTime;
	

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'recordingId',
		'description',
		'contentUrl',
		'urlExpiry',
		'fileExtension',
		'meetingId',
		'recordingStartTime',
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

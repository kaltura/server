<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage api.objects
 */
class KalturaFeedDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * MD5 or Sha1 encrypted string
	 * @var string
	 */
	public $hash;
	
	/**
	 * Path of the original Feed content XML
	 * @var string
	 */
	public $feedXmlPath;
	
/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'hash',
		'feedXmlPath'
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new FeedDropFolderFile();
			
		return parent::toObject($dbObject, $skip);
	}
}
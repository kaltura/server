<?php
/**
 * @package plugins.dropFolderMRSS
 * @subpackage api.objects
 */
class KalturaMRSSDropFolderFile extends KalturaDropFolderFile
{
	
	/**
	 * Local path of the MRSS XML
	 * @var string
	 */
	public  $xmlLocalPath;	
	
	/**
	 * MD5 or Sha1 encrypted string
	 * @var string
	 */
	public $hash;
	
	/**
	 * Flag indicating whether a content update is required
	 * @var bool
	 */
	public $contentUpdateRequired;
	
/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'xmlLocalPath',
		'hash',
		'contentUpdateRequired',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new MRSSDopFolderFile();
		
		return parent::toObject($dbObject, $skip);
	}
}
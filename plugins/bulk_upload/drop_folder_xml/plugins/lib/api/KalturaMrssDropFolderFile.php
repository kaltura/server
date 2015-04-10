<?php
/**
 * @package plugins.DropFolderMrss
 * @subpackage api.objects
 */
class KalturaMrssDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * MD5 or Sha1 encrypted string
	 * @var string
	 */
	public $hash;
	
	/**
	 * MRSS content of the
	 * @var string
	 */
	public $mrssContent;
	
/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'hash',
		'mrssContent'
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new MrssDropFolderFile();
			
		return parent::toObject($dbObject, $skip);
	}
}
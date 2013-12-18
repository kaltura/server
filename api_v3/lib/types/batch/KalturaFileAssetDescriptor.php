<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFileAssetDescriptor extends KalturaObject
{
	/**
	 * @var string
	 */
	public $fileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	public $fileSyncRemoteUrl;
	
	/**
	 * @var string
	 */
	public $fileExt;
	
	/**
	 * 
	 * @var string
	 */
	public $name;
	
	private static $map_between_objects = array
	(
		"fileSyncLocalPath" ,
		"fileSyncRemoteUrl" ,
		"fileExt" ,
		"name" ,
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kFileAssetDescriptor();
			
		return parent::toObject($dbObject, $skip);
	}
}
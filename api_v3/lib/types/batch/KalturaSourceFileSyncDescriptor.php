<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSourceFileSyncDescriptor extends KalturaObject
{
	/**
	 * @var string
	 */
	public $fileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	public $actualFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	public $fileSyncRemoteUrl;
	
	/**
	 * 
	 * @var string
	 */
	public $assetId;
	
	/**
	 * 
	 * @var int
	 */
	public $assetParamsId;

	/**
	 * 
	 * @var int
	*/
	public $fileSyncObjectSubType;
	
	private static $map_between_objects = array
	(
		"fileSyncLocalPath" ,
		"actualFileSyncLocalPath" ,
		"fileSyncRemoteUrl" ,
		"assetId" ,
		"assetParamsId" ,
		"fileSyncObjectSubType" ,
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kSourceFileSyncDescriptor();
			
		return parent::toObject($dbObject, $skip);
	}
}
<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGCPStorageDeleteJobData extends KalturaStorageDeleteJobData
{
	/**
	 * @var string
	 */
	public $bucketName;

	/**
	 * @var string
	 */
	public $keyFile;
    
    private static $map_between_objects = array
	(
		'bucketName',
		'keyFile',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kGCPStorageDeleteJobData();
			
		return parent::toObject($dbData);
	}
	
}
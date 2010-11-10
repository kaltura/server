<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaVirusScanJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcFilePath;
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var bool
	 */
	public $infected;
	
	private static $map_between_objects = array
	(
		"srcFilePath" ,
		"flavorAssetId" ,
		"infected" ,
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

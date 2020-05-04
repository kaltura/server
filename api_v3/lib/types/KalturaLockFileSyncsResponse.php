<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLockFileSyncsResponse extends KalturaObject
{
	/**
	 * @var KalturaFileSyncArray
	 */
	public $fileSyncs;
		
	/**
	 * @var bool
	 */
	public $limitReached;
	
	/**
	 * @var string
	 */
	public $dcSecret;
	
	/**
	 * @var string
	 */
	public $baseUrl;
	
	private static $map_between_objects = array
	(
		"fileSyncs",
		"limitReached",
		"dcSecret",
		"baseUrl",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

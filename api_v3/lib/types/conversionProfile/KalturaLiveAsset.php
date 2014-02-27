<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveAsset extends KalturaFlavorAsset 
{
	/**
	 * @var string
	 * @requiresPermission all
	 */
	public $multicastIP;
	
	/**
	 * @var int
	 * @requiresPermission all
	 */
	public $multicastPort;
	
	private static $map_between_objects = array
	(
		"multicastIP",
		"multicastPort",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

<?php
/**
 * @package plugins.widevine
 * @subpackage api.objects
 */
class KalturaWidevineRepositorySyncJobData extends KalturaJobData
{
	/**
	 * 
	 * @var int
	 */
	public $syncMode;
	
	/**
	 * @var string
	 */
	public $wvAssetIds;
	
	/**
	 * @var string
	 */
	public $modifiedAttributes;
		
	private static $map_between_objects = array
	(
		"syncMode",
		"wvAssetIds",
		"modifiedAttributes",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
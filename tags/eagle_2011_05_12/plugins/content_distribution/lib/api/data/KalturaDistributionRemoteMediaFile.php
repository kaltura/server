<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionRemoteMediaFile extends KalturaObject
{
	/**
	 * @var string
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $assetId;
	
	/**
	 * @var string
	 */
	public $remoteId;
	
	
	private static $map_between_objects = array
	(
		"version" ,
		"assetId" ,
		"remoteId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kDistributionRemoteMediaFile();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}

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
}

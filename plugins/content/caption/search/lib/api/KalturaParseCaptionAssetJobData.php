<?php
/**
 * @package plugins.virusScan
 * @subpackage api.objects
 */
class KalturaParseCaptionAssetJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $captionAssetId;
	
	
	private static $map_between_objects = array
	(
		"captionAssetId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

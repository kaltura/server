<?php
/**
 * @package plugins.visualRecognition
 * @subpackage api.objects
 */
class KalturaVisualRecognitionJobProviderData extends KalturaIntegrationJobProviderData
{
	/**
	 *
	 * @var string
	 */
	public $recognizeElementURL;
	
	private static $map_between_objects = array
	(
		"recognizeElementURL" ,
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

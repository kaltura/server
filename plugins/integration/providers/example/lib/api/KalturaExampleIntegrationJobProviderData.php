<?php
/**
 * @package plugins.exampleIntegration
 * @subpackage api.objects
 */
class KalturaExampleIntegrationJobProviderData extends KalturaIntegrationJobProviderData
{
	/**
	 * Just an example
	 * 
	 * @var string
	 */
	public $exampleUrl;
	
	private static $map_between_objects = array
	(
		"exampleUrl" ,
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

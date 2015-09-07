<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaDeliveryServerNode extends KalturaServerNode
{	
	/**
	 * remoteServer host name
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $playbackHostName;
		
	private static $map_between_objects = array 
	(
		"playbackHostName",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
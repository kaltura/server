<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaDeliveryServerNode extends KalturaServerNode
{	
	/**
	 * Delivery server playback Domain
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $playbackDomain;
		
	private static $map_between_objects = array 
	(
		"playbackDomain" => "playbackHostName",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
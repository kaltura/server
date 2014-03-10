<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyContentKey extends KalturaObject 
{
	/**
	 * Guid - key id of the specific content 
	 * 
	 * @var string
	 */
	public $keyId;
	
	/**
	 * License content key 64 bit encoded
	 * 
	 * @var string
	 */
	public $contentKey;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'keyId',
		'contentKey',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
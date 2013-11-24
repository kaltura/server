<?php
/**
 * @package plugins.widevine
 * @subpackage api.objects
 */
class KalturaWidevineProfile extends KalturaDrmProfile
{
    /**
	 * @var string
	 */
	public $key;

	/**
	 * @var string
	 */
	public $iv;
	
	/**
	 * @var string
	 */
	public $owner;
		
	/**
	 * @var string
	 */
	public $portal;

	/**
	 * @var int
	 */	
	public $maxGop;

	/**
	 * @var string
	 */
	public $regServerHost;
	
	
	private static $map_between_objects = array(
		'key',
		'iv',
		'owner',
		'portal',
		'maxGop',
		'regServerHost'
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new WidevineProfile();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
	
}


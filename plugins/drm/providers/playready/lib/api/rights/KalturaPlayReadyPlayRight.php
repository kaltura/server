<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyPlayRight extends KalturaPlayReadyRight
{
    /**
	 * @var KalturaPlayReadyAnalogVideoOPL
	 */
	public $analogVideoOPL ;
	
	/**
	 * @var KalturaPlayReadyAnalogVideoOPIdHolderArray
	 */
	public $analogVideoOutputProtectionList ;
	
    /**
	 * @var KalturaPlayReadyDigitalAudioOPL
	 */
	public $compressedDigitalAudioOPL ;
	
    /**
	 * @var KalturaPlayReadyCompressedDigitalVideoOPL
	 */
	public $compressedDigitalVideoOPL ;

	/**
	 * @var KalturaPlayReadyDigitalAudioOPIdHolderArray
	 */
	public $digitalAudioOutputProtectionList; 
	
	/**
	 * @var KalturaPlayReadyDigitalAudioOPL
	 */	
	public $uncompressedDigitalAudioOPL;

    /**
	 * @var KalturaPlayReadyUncompressedDigitalVideoOPL
	 */
	public $uncompressedDigitalVideoOPL; 
	
    /**
	 * @var int
	 */
	public $firstPlayExpiration;
	
    /**
	 * @var KalturaPlayReadyPlayEnablerHolderArray
	 */
	public $playEnablers; 
	
	
	private static $map_between_objects = array(
		'analogVideoOPL',
    	'analogVideoOutputProtectionList',
    	'compressedDigitalAudioOPL',
    	'compressedDigitalVideoOPL',
		'digitalAudioOutputProtectionList',
		'uncompressedDigitalAudioOPL',
		'uncompressedDigitalVideoOPL',
		'firstPlayExpiration',
		'playEnablers',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new PlayReadyPlayRight();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
}



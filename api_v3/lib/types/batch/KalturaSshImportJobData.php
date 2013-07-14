<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaSshImportJobData extends KalturaImportJobData
{    
	/**
	 * @var string
	 */
	public $privateKey;
	
	/**
	 * @var string
	 */
	public $publicKey;
	
	/**
	 * @var string
	 */
	public $passPhrase;
	
	
	private static $map_between_objects = array
	(
    	"privateKey",
    	"publicKey",
	    "passPhrase",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kSshImportJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}


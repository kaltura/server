<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaProvisionJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $streamID;
	
	/**
	 * @var string
	 */
	public $backupStreamID;
	
	/**
	 * @var string
	 */
	public $rtmp;
 	
	/**
	 * @var string
	 */
	public $encoderIP;
 	
	/**
	 * @var string
	 */
	public $backupEncoderIP;
 	
	/**
	 * @var string
	 */
	public $encoderPassword;
 	
	/**
	 * @var string
	 */
	public $encoderUsername;
 	
	/**
	 * @var int
	 */
	public $endDate;
 	
	/**
	 * @var string
	 */
	public $returnVal;
	
	/**
	 * @var int
	 */
	public $mediaType;
    
	private static $map_between_objects = array
	(
		"streamID",
		"backupStreamID",
		"rtmp",
		"encoderIP",
		"backupEncoderIP",
		"encoderPassword",
		"encoderUsername",
		"endDate",
		"returnVal",
		"mediaType",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kProvisionJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>
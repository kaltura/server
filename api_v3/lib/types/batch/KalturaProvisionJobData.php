<?php
/**
 * @package api
 * @subpackage objects
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
	
	/**
	 * @var string
	 */
	public $primaryBroadcastingUrl;
	
	/**
	 * @var string
	 */
	public $secondaryBroadcastingUrl;
	
	/**
	 * @var string
	 */
	public $streamName;
    
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
		"primaryBroadcastingUrl",
		"secondaryBroadcastingUrl",
		"streamName",
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
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		// TODO - change to pluginable enum to support more providers
		return $subType;
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		switch ($subType)
		{
			case KalturaSourceType::AKAMAI_LIVE:
			case KalturaSourceType::AKAMAI_UNIVERSAL_LIVE:
				return $subType;
				break;
			default:
				return kPluginableEnumsManager::coreToApi('EntrySourceType', $subType);
				break;
		}
	}
	

	/**
	 * Return instance of KalturaProvisionJobData according to job sub-type
	 * @param int $jobSubType
	 * @return KalturaProvisionJobData
	 */
	public static function getJobDataInstance ($jobSubType)
	{
		KalturaLog::info ("Determining correct job data based on jobSubType $jobSubType");
		switch ($jobSubType)
		{
			case KalturaSourceType::AKAMAI_LIVE:
				return new KalturaAkamaiProvisionJobData();
				break;
			case KalturaSourceType::AKAMAI_UNIVERSAL_LIVE:
				return new KalturaAkamaiUniversalProvisionJobData();
				break;
			default:
				return KalturaPluginManager::loadObject('KalturaProvisionJobData', $jobSubType);
				break;
		
		}
	}
}

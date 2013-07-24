<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 * 
	 * @var string
	 */
	public $offlineMessage;
	
	/**
	 * The stream id as provided by the provider
	 * 
	 * @var string
	 * @readonly
	 */
	public $streamRemoteId;
	
	/**
	 * The backup stream id as provided by the provider
	 * 
	 * @var string
	 * @readonly
	 */
	public $streamRemoteBackupId;
	
	/**
	 * Array of supported bitrates
	 * 
	 * @var KalturaLiveStreamBitrateArray
	 */
	public $bitrates;
	
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
	
	/**
	 * The stream url
	 * 
	 * @var string
	 */
	public $streamUrl;
	
	/**
	 * HLS URL - URL for live stream playback on mobile device
	 * @var string
	 */
	public $hlsStreamUrl;
	
	/**
	 * DVR Status Enabled/Disabled
	 * @var KalturaDVRStatus
	 * @insertonly
	 */
	public $dvrStatus;
	
	/**
	 * Window of time which the DVR allows for backwards scrubbing (in minutes)
	 * @var int
	 * @insertonly
	 */
	public $dvrWindow;
	
	/**
	 * URL Manager to handle the live stream URL (for instance, add token)
	 * @var string
	 */
	public $urlManager;
	
	/**
	 * Array of key value protocol->live stream url objects
	 * @var KalturaLiveStreamConfigurationArray
	 */
	public $liveStreamConfigurations;
	
	/**
	 * The broadcast primary ip
	 * @requiresPermission all
	 * @var string
	 */
	public $encodingIP1;
	
	/**
	 * The broadcast secondary ip
	 * 
	 * @requiresPermission all
	 * @var string
	 */
	public $encodingIP2;
	
	/**
	 * The broadcast password
	 * 
	 * @requiresPermission all
	 * @var string
	 */
	public $streamPassword;
	
	/**
	 * The broadcast username
	 * 
	 * @requiresPermission read
	 * @var string
	 * @readonly
	 */
	public $streamUsername;
	
	
	
	private static $map_between_objects = array
	(
		"offlineMessage",
		"streamRemoteId",
	 	"streamRemoteBackupId",
		"primaryBroadcastingUrl",
		"secondaryBroadcastingUrl",
		"streamName",
		"streamUrl",
	    "hlsStreamUrl",
	    "dvrStatus",
	    "dvrWindow",
	    "urlManager",
		"liveStreamConfigurations",
		"encodingIP1",
		"encodingIP2",
		"streamPassword",
		"streamUsername",
	);

	public function __construct()
	{
		parent::__construct();
		
		$this->type = KalturaEntryType::LIVE_STREAM;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::fromObject()
	 */
	public function fromObject ( $dbObject )
	{
		if(!($dbObject instanceof entry))
			return;
			
		parent::fromObject($dbObject);

		$bitrates = $dbObject->getStreamBitrates();
		if(is_array($bitrates))
			$this->bitrates = KalturaLiveStreamBitrateArray::fromLiveStreamBitrateArray($bitrates);
		
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::toObject()
	 */
	public function toObject ( $dbObject = null , $props_to_skip = array() )
	{
		parent::toObject($dbObject, $props_to_skip);
		
		if($this->bitrates)
			$dbObject->setStreamBitrates($this->bitrates->toArray());
				
		return $dbObject;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntry::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("mediaType");
		$this->validatePropertyNotNull("sourceType");
		$this->validatePropertyNotNull("streamPassword");
		if (in_array($this->sourceType, array(KalturaSourceType::AKAMAI_LIVE,KalturaSourceType::AKAMAI_UNIVERSAL_LIVE)))
		{
			$this->validatePropertyNotNull("encodingIP1");
			$this->validatePropertyNotNull("encodingIP2");
			$this->validateEncodingIP($this->encodingIP1);
			$this->validateEncodingIP($this->encodingIP2);
		}
	}
	
	protected function validateEncodingIP ($ip)
	{
		if (!filter_var($this->encodingIP1, FILTER_VALIDATE_IP))
			throw new KalturaAPIException(KalturaErrors::ENCODING_IP_NOT_PINGABLE);	
		
		@exec("ping -w " . kConf::get('ping_default_timeout') . " {$this->encodingIP1}", $output, $return);
		if ($return)
			throw new KalturaAPIException(KalturaErrors::ENCODING_IP_NOT_PINGABLE);
	}
}

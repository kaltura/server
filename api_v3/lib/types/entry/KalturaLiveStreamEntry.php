<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamEntry extends KalturaLiveEntry
{
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
	public $primarySecuredBroadcastingUrl;

	/**
	 * @var string
	 */
	public $secondarySecuredBroadcastingUrl;
	
	/**
	 * @var string
	 * @deprecated
	 */
	public $primaryRtspBroadcastingUrl;
	
	/**
	 * @var string
	 * @deprecated
	 */
	public $secondaryRtspBroadcastingUrl;

	/**
	 * @var string
	 */
	public $primarySrtBroadcastingUrl;

	/**
	 * @var string
	 */
	public $primarySrtStreamId;

	/**
	 * @var string
	 */
	public $secondarySrtBroadcastingUrl;

	/**
	 * @var string
	 */
	public $secondarySrtStreamId;
	
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
	 * URL Manager to handle the live stream URL (for instance, add token)
	 * @var string
	 * @deprecated
	 */
	public $urlManager;
	
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

	/**
	 * @var string
	 */
	public $srtPass;
	
	/**
	 * The Streams primary server node id 
	 *
	 * @var int
	 * @readonly
	 */
	public $primaryServerNodeId;

	/**
	 * @var string
	 * @readonly
	 */
	public $sipToken;

	/**
	 * @var KalturaSipSourceType
	 * @readonly
	 */
	public $sipSourceType;

	private static $map_between_objects = array
	(
		"streamRemoteId",
	 	"streamRemoteBackupId",
		"primaryBroadcastingUrl",
		"secondaryBroadcastingUrl",
		"primarySecuredBroadcastingUrl",
		"secondarySecuredBroadcastingUrl",
		"primaryRtspBroadcastingUrl",
		"secondaryRtspBroadcastingUrl",
		"primarySrtBroadcastingUrl",
		"secondarySrtBroadcastingUrl",
		"primarySrtStreamId",
		"secondarySrtStreamId",
		"streamName",
		"streamUrl",
	    "hlsStreamUrl",
		"encodingIP1",
		"encodingIP2",
		"streamPassword",
		"streamUsername",
		"srtPass",
		"bitrates" => "streamBitrates",
		"primaryServerNodeId",
		"sipToken",
		"sipSourceType"
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
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!($dbObject instanceof LiveStreamEntry))
			return;
		/**
		 * @var LiveStreamEntry @dbObject
		 */
		$ksObject = kCurrentContext::$ks_object;
		if ( !kCurrentContext::$is_admin_session && !(kCurrentContext::getCurrentKsKuserId() == $dbObject->getKuserId())
				&& !($dbObject->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId())) && (!$ksObject || !$ksObject->verifyPrivileges(ks::PRIVILEGE_EDIT, $this->id)) )
		{
			$this->primaryBroadcastingUrl = null;
			$this->secondaryBroadcastingUrl = null;
			$this->primarySecuredBroadcastingUrl = null;
			$this->secondarySecuredBroadcastingUrl = null;
			$this->primaryRtspBroadcastingUrl = null;
			$this->secondaryRtspBroadcastingUrl = null;
			$this->primarySrtBroadcastingUrl = null;
			$this->primarySrtStreamId = null;
			$this->secondarySrtBroadcastingUrl = null;
			$this->secondarySrtStreamId = null;
			$this->srtPass = null;
		}
		parent::doFromObject($dbObject, $responseProfile);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::toInsertableObject()
	 */
	public function toInsertableObject ( $dbObject = null , $props_to_skip = array() )
	{
		//This is required for backward compatibility support of api calls in KMS
		$propertiesToSkip[] = "id";
		
		/* @var $dbObject LiveStreamEntry */
		
		// if the given password is empty, generate a new password
		if(($this->streamPassword == null) || (strlen(trim($this->streamPassword)) <= 0))
		{
			$this->streamPassword = LiveStreamEntry::generateStreamPassword();
		}
	
		return parent::toInsertableObject($dbObject, $props_to_skip);
	}

	public function toUpdatableObject($object_to_fill, $props_to_skip = array())
	{
		if(strpos(strtolower(kCurrentContext::$client_lang), "kmc") !== false)
		{
			$props_to_skip[] = LiveStreamEntry::PRIMARY_BROADCASTING_URL;
			$props_to_skip[] = LiveStreamEntry::SECONDARY_BROADCASTING_URL;
			$props_to_skip[] = LiveStreamEntry::PRIMARY_RTMPS_BROADCASTING_URL;
			$props_to_skip[] = LiveStreamEntry::SECONDARY_RTMPS_BROADCASTING_URL;
			$props_to_skip[] = LiveStreamEntry::PRIMARY_RTSP_BROADCASTING_URL;
			$props_to_skip[] = LiveStreamEntry::SECONDARY_RTSP_BROADCASTING_URL;
		}

		return parent::toUpdatableObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::toSourceType()
	 */
	protected function toSourceType(entry $entry) 
	{
		if (!$this->sourceType)
		{
			$partner = PartnerPeer::retrieveByPK(kCurrentContext::getCurrentPartnerId());
			if($partner)
				$this->sourceType = kPluginableEnumsManager::coreToApi('EntrySourceType', $partner->getDefaultLiveStreamEntrySourceType());
		}
		
		return parent::toSourceType($entry);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntry::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		//This is required for backward compatibility support of api calls in KMS
		$propertiesToSkip[] = "id";
		
		$this->validatePropertyNotNull("mediaType");
		$this->validatePropertyNotNull("streamPassword");
		$this->validatePropertyNotNull("sourceType");
		if ($this->sourceType == KalturaSourceType::AKAMAI_UNIVERSAL_LIVE)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_SOURCE_TYPE_NOT_SUPPORTED, $this->sourceType);
		}
		if ($this->sourceType == KalturaSourceType::AKAMAI_LIVE)
		{
			$this->validatePropertyNotNull("encodingIP1");
			$this->validatePropertyNotNull("encodingIP2");
		}

		$this->validatePropertyMinMaxLength('srtPass', 10, 79, true);

		parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinMaxLength('srtPass', 10, 79, true);

		parent::validateForUpdate($sourceObject, $propertiesToSkip);
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

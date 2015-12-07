<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaLiveEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 * 
	 * @var string
	 */
	public $offlineMessage;
	
	/**
	 * Recording Status Enabled/Disabled
	 * @var KalturaRecordStatus
	 * @insertonly
	 */
	public $recordStatus;
	
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
	 * Elapsed recording time (in msec) up to the point where the live stream was last stopped (unpublished).
	 * @var int
	 */
	public $lastElapsedRecordingTime;

	/**
	 * Array of key value protocol->live stream url objects
	 * @var KalturaLiveStreamConfigurationArray
	 */
	public $liveStreamConfigurations;
	
	/**
	 * Recorded entry id
	 * 
	 * @var string
	 */
	public $recordedEntryId;
	

	/**
	 * Flag denoting whether entry should be published by the media server
	 * 
	 * @var KalturaLivePublishStatus
	 * @requiresPermission all
	 */
	public $pushPublishEnabled;
	
	/**
	 * Array of publish configurations
	 * 
	 * @var KalturaLiveStreamPushPublishConfigurationArray
	 * @requiresPermission all
	 */
	public $publishConfigurations;
	
	/**
	 * The first time in which the entry was broadcast
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $firstBroadcast;
	
	/**
	 * The Last time in which the entry was broadcast
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $lastBroadcast;
	
	/**
	 * The time (unix timestamp in milliseconds) in which the entry broadcast started or 0 when the entry is off the air
	 * @var float
	 */
	public $currentBroadcastStartTime;
	
	/**
	 * @var KalturaLiveEntryRecordingOptions
	 * @insertonly
	 */
	public $recordingOptions;
	
	private static $map_between_objects = array
	(
		"offlineMessage",
	    "recordStatus",
	    "dvrStatus",
	    "dvrWindow",
		"lastElapsedRecordingTime",
		"liveStreamConfigurations",
		"recordedEntryId",
		"pushPublishEnabled",
		"firstBroadcast",
		"lastBroadcast",
		"publishConfigurations",
		"currentBroadcastStartTime",
		"recordingOptions",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toInsertableObject($sourceObject = null, $propsToSkip = array())
	{
		if(is_null($this->recordStatus))
			$this->recordStatus = (PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM_RECORD, kCurrentContext::getCurrentPartnerId()) ? KalturaRecordStatus::APPENDED : KalturaRecordStatus::DISABLED);


		if ((is_null($this->recordingOptions) || is_null($this->recordingOptions->shouldCopyEntitlement)) && PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM_COPY_ENTITELMENTS, kCurrentContext::getCurrentPartnerId()))
		{
			if (is_null($this->recordingOptions))
			{
				$this->recordingOptions = new KalturaLiveEntryRecordingOptions();
			}
			$this->recordingOptions->shouldCopyEntitlement = true;
		}
		return parent::toInsertableObject($sourceObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!($dbObject instanceof LiveEntry))
			return;
			
		parent::doFromObject($dbObject, $responseProfile);

		if($this->shouldGet('recordingOptions', $responseProfile) && !is_null($dbObject->getRecordingOptions()))
		{
			$this->recordingOptions = new KalturaLiveEntryRecordingOptions();
			$this->recordingOptions->fromObject($dbObject->getRecordingOptions());
		}
	}

	public function validateConversionProfile(entry $sourceObject = null)
	{
		if(!is_null($this->conversionProfileId) && $this->conversionProfileId != conversionProfile2::CONVERSION_PROFILE_NONE)
		{
			$conversionProfile = conversionProfile2Peer::retrieveByPK($this->conversionProfileId);
			if(!$conversionProfile || $conversionProfile->getType() != ConversionProfileType::LIVE_STREAM)
				throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $this->conversionProfileId);
		}
	}
}

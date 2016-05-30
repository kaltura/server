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
	 */
	public $recordStatus;
	
	/**
	 * DVR Status Enabled/Disabled
	 * @var KalturaDVRStatus
	 */
	public $dvrStatus;
	
	/**
	 * Window of time which the DVR allows for backwards scrubbing (in minutes)
	 * @var int
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
	 */
	public $recordingOptions;

	/**
	 * the status of the entry of type EntryServerNodeStatus
	 * @var KalturaEntryServerNodeStatus
	 * @readonly
	 * @deprecated use KalturaLiveStreamService.isLive instead
	 */
	public $liveStatus;

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
		"liveStatus"
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($source_object)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$updateValidateAttributes = array(
				"dvrStatus" => array("validatePropertyChanged"), 
				"dvrWindow" => array("validatePropertyChanged"), 
				"recordingOptions" => array("validateRecordingOptionsChanged"),
				"recordStatus" => array("validatePropertyChanged","validateRecordedEntryId"), 
				"conversionProfileId" => array("validatePropertyChanged","validateRecordedEntryId")
		);
		
		foreach ($updateValidateAttributes as $attr => $validateFucntions)
		{
			if(isset($this->$attr))
			{
				foreach ($validateFucntions as $function)
				{
					$this->$function($sourceObject, $attr);
				}
			}
		}
		
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	protected function validatePropertyChanged($sourceObject, $attr)
	{
		$resolvedAttrName = $this->getObjectPropertyName($attr);
		if(!$resolvedAttrName)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $attr, get_class($this));
		
		/* @var $sourceObject LiveEntry */
		$getter = "get" . ucfirst($resolvedAttrName);
		if($sourceObject->$getter() !== $this->$attr && $sourceObject->getLiveStatus() !== KalturaEntryServerNodeStatus::STOPPED)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_FIELDS_WHILE_ENTRY_BROADCASTING, $attr);
		}
	}
	
	protected function validateRecordedEntryId($sourceObject, $attr)
	{
		$resolvedAttrName = $this->getObjectPropertyName($attr);
		if(!$resolvedAttrName)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $attr, get_class($this));
		
		/* @var $sourceObject LiveEntry */
		$getter = "get" . ucfirst($resolvedAttrName);
		if($sourceObject->$getter() !== $this->$attr)
		{
			$this->validateRecordingDone($sourceObject, $attr);
		}
	}
	
	private function validateRecordingDone($sourceObject, $attr)
	{
		/* @var $sourceObject LiveEntry */
		$recordedEntry = $sourceObject->getRecordedEntryId() ? entryPeer::retrieveByPK($sourceObject->getRecordedEntryId()) : null;
		if($recordedEntry)
		{
			$validUpdateStatuses = array(KalturaEntryStatus::READY, KalturaEntryStatus::ERROR_CONVERTING, KalturaEntryStatus::ERROR_IMPORTING);
			if( !in_array($recordedEntry->getStatus(), $validUpdateStatuses) )
				throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_FIELDS_RECORDED_ENTRY_STILL_NOT_READY, $attr);
			
			$noneReadyAssets = assetPeer::retrieveByEntryId($recordedEntry->getId(),
					array(KalturaAssetType::FLAVOR),
					array(KalturaFlavorAssetStatus::CONVERTING, KalturaFlavorAssetStatus::QUEUED, KalturaFlavorAssetStatus::WAIT_FOR_CONVERT, KalturaFlavorAssetStatus::VALIDATING));
			
			if(count($noneReadyAssets))
				throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_FIELDS_RECORDED_ENTRY_STILL_NOT_READY, $attr);
		}
	}
	
	protected function validateRecordingOptionsChanged($sourceObject, $attr)
	{
		if(!isset($this->recordingOptions))
			return;
		
		if(!isset($this->recordingOptions->shouldCopyEntitlement))
			return;
		
		/* @var $sourceObject LiveEntry */
		$hasObjectChanged = false;
		if( !$sourceObject->getRecordingOptions() || ($sourceObject->getRecordingOptions() && $sourceObject->getRecordingOptions()->getShouldCopyEntitlement() !== $this->recordingOptions->shouldCopyEntitlement) )
			$hasObjectChanged = true;
		
		if($hasObjectChanged)
		{
			if( $sourceObject->getLiveStatus() !== KalturaEntryServerNodeStatus::STOPPED)
				throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_FIELDS_WHILE_ENTRY_BROADCASTING, "recordingOptions");
			
			$this->validateRecordingDone($sourceObject, "recordingOptions");
		}
	}
}

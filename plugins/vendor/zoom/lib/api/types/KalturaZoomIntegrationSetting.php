<?php
/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
class KalturaZoomIntegrationSetting extends KalturaIntegrationSetting
{
	/**
	 * @var string
	 */
	public $zoomCategory;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableRecordingUpload;

	/**
	 * @var KalturaZoomUsersMatching
	 */
	public $zoomUserMatchingMode;

	/**
	 * @var string
	 */
	public $zoomUserPostfix;

	/**
	 * @var string
	 */
	public $zoomWebinarCategory;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableWebinarUploads;

	/**
	 * @var string
	 */
	public $jwtToken;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableZoomTranscription;
	
	/**
	 * @var string
	 */
	public $zoomAccountDescription;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableMeetingUpload;
	
	/**
	 * @var string
	 */
	public $optOutGroupNames;
	
	/**
	 * @var string
	 */
	public $optInGroupNames;
	
	/**
	 * @var KalturaZoomGroupParticipationType
	 */
	public $groupParticipationType;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'zoomCategory',
		'zoomUserMatchingMode' => 'UserMatching',
		'zoomUserPostfix' => 'UserPostfix',
		'zoomWebinarCategory',
		'enableWebinarUploads',
		'enableRecordingUpload' => 'status',
		'jwtToken',
		'enableZoomTranscription',
		'zoomAccountDescription',
		'enableMeetingUpload',
		'groupParticipationType',
		'optInGroupNames',
		'optOutGroupNames',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new ZoomVendorIntegration();
		}
		
		parent::toObject($dbObject, $skip);
		$dbObject->setStatus($this->enableRecordingUpload ? VendorIntegrationStatus::ACTIVE : VendorIntegrationStatus::DISABLED);
		
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
		$this->enableRecordingUpload = $sourceObject->getStatus() == VendorIntegrationStatus::ACTIVE ? 1 : 0;
		
		$dropFolderType = ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM);
		$dropFolders = DropFolderPeer::retrieveEnabledDropFoldersPerPartner($sourceObject->getPartnerId(), $dropFolderType);
		$relatedDropFolder = null;
		foreach ($dropFolders as $dropFolder)
		{
			if ($dropFolder->getZoomVendorIntegrationId() == $sourceObject->getId())
			{
				$relatedDropFolder = $dropFolder;
				break;
			}
		}
		if (!$relatedDropFolder)
		{
			$this->enableZoomTranscription = null;
			$this->deletionPolicy = null;
			$this->enableMeetingUpload = null;
		}
	}
}
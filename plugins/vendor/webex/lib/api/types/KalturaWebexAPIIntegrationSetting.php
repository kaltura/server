<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */
class KalturaWebexAPIIntegrationSetting extends KalturaIntegrationSetting
{
	/**
	 * @var string
	 */
	public $webexCategory;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableRecordingUpload;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableTranscription;
	
	/**
	 * @var KalturaWebexAPIUsersMatching
	 */
	public $userMatchingMode;
	
	/**
	 * @var string
	 */
	public $userPostfix;
	
	/**
	 * @var string
	 */
	public $webexAccountDescription;
	
	/**
	 * @var KalturaWebexAPIGroupParticipationType
	 */
	public $groupParticipationType;
	
	/**
	 * @var string
	 */
	public $optOutGroupNames;
	
	/**
	 * @var string
	 */
	public $optInGroupNames;
	
	/**
	 * @var string
	 */
	public $siteUrl;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'webexCategory',
		'enableRecordingUpload' => 'status',
		'enableTranscription',
		'userMatchingMode',
		'userPostfix',
		'webexAccountDescription',
		'groupParticipationType',
		'optInGroupNames',
		'optOutGroupNames',
		'siteUrl',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new WebexAPIVendorIntegration();
		}
		
		parent::toObject($dbObject, $skip);
		$dbObject->setStatus($this->enableRecordingUpload ? VendorIntegrationStatus::ACTIVE : VendorIntegrationStatus::DISABLED);
		
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if (!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
		$this->enableRecordingUpload = $sourceObject->getStatus() == VendorIntegrationStatus::ACTIVE ? 1 : 0;
		
		$dropFolderType = WebexAPIDropFolderPlugin::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API);
		$dropFolders = DropFolderPeer::retrieveEnabledDropFoldersPerPartner($sourceObject->getPartnerId(), $dropFolderType);
		$relatedDropFolder = null;
		foreach ($dropFolders as $dropFolder)
		{
			if ($dropFolder->getWebexAPIVendorIntegrationId() == $sourceObject->getId())
			{
				$relatedDropFolder = $dropFolder;
				break;
			}
		}
		if (!$relatedDropFolder)
		{
			$this->enableMeetingUpload = false;
		}
	}
	
	public function toInsertableObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new WebexAPIVendorIntegration();
		}
		
		return parent::toInsertableObject($dbObject);
	}
}

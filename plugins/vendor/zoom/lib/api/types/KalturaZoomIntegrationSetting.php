<?php
/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
class KalturaZoomIntegrationSetting extends KalturaObject
{
	/**
	 * @var string
	 */
	public $defaultUserId;

	/**
	 * @var string
	 */
	public $zoomCategory;

	/**
	 * @var string
	 * @readonly
	 */
	public $accountId;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableRecordingUpload;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $createUserIfNotExist;

	/**
	 * @var KalturaHandleParticipantsMode
	 */
	public $handleParticipantsMode;

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
	* @var int
	 */
	public $conversionProfileId;
	
	/**
	 * @var string
	 */
	public $jwtToken;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $deletionPolicy;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableZoomTranscription;
	
	/**
	 * @var string
	 */
	public $zoomAccountDescription;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'zoomCategory',
		'accountId',
		'createUserIfNotExist',
		'handleParticipantsMode',
		'zoomUserMatchingMode' => 'UserMatching',
		'zoomUserPostfix' => 'UserPostfix',
		'zoomWebinarCategory',
		'enableWebinarUploads',
		'enableRecordingUpload' => 'status',
		'conversionProfileId',
		'defaultUserId' => 'defaultUserEMail',
		'jwtToken',
		'deletionPolicy',
		'enableZoomTranscription',
		'zoomAccountDescription',
		'createdAt',
		'updatedAt'
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
		$dbObject->setStatus($this->enableRecordingUpload ? VendorStatus::ACTIVE : VendorStatus::DISABLED);
		$dbObject->setJwtToken($this->jwtToken);
		$dbObject->setDeletionPolicy($this->deletionPolicy);
		$dbObject->setEnableZoomTranscription($this->enableZoomTranscription);
		
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
		$this->enableRecordingUpload = $sourceObject->getStatus() == VendorStatus::ACTIVE ? 1 : 0;
	}
}
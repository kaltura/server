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
	public $enableMeetingUpload;
	
	/**
	 * @var KalturaZoomUsersMatching
	 */
	public $userMatchingMode;
	
	/**
	 * @var string
	 */
	public $userPostfix;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'webexCategory',
		'enableRecordingUpload' => 'status',
		'enableMeetingUpload',
		'userMatchingMode',
		'userPostfix',
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
		
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
	}
}
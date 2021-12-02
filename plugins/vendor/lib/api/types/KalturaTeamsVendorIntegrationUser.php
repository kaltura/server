<?php


class KalturaTeamsVendorIntegrationUser extends KalturaVendorIntegrationUser
{
	/**
	 * @var string
	 */
	public $userId;

	/**
	 * @var string
	 */
	public $teamsId;

	/**
	 * @var string
	 */
	public $recordingsFolderId;

	/**
	 * @var string
	 */
	public $deltaToken;


	private static $map_between_objects = array('userId', 'teamsId', 'recordingsFolderId', 'deltaToken');

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new kuser();
		}
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if (!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
	}

}
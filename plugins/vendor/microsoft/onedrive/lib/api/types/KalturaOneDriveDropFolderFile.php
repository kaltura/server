<?php

/**
 * @package plugins.OneDrive
 * @subpackage api.objects
 */
class KalturaOneDriveDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * @var string
	 */
	public $remoteId;

	/**
	 * @var string
	 */
	public $ownerId;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $additionalUserIds;
	
	/**
	 * @var string
	 */
	public $targetCategoryIds;
	
	/**
	 * @var string
	 */
	public $contentUrl;
	
	/**
	 * @var string
	 */
	public $driveId;
	
	/**
	 * @var int
	 */
	public $tokenExpiry;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'remoteId',
		'ownerId',
		'name',
		'description',
		'additionalUserIds',
		'targetCategoryIds',
		'contentUrl',
		'driveId',
		'tokenExpiry',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new OneDriveDropFolderFile();

		return parent::toObject($dbObject, $skip);
	}
}
<?php

/**
 * @package plugins.MicrosoftTeamsDropFolder
 * @subpackage api.objects
 */
class KalturaMicrosoftTeamsDropFolderFile extends KalturaDropFolderFile
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
	public $additionalUserIds;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $targetCategoryIds;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $contentUrl;


	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'remoteId',
		'ownerId',
		'additionalUserIds',
		'description',
		'name',
		'targetCategoryIds',
		'contentUrl',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new MicrosoftTeamsDropFolderFile();

		return parent::toObject($dbObject, $skip);
	}
}
<?php

/**
 * @package plugins.MicrosoftTeamsDropFolder
 * @subpackage api.objects
 */
class KalturaMicrosoftTeamsIntegrationSetting extends KalturaIntegrationSetting
{
	/**
	 * @var string
	 */
	public $clientSecret;

	/**
	 * @var string
	 */
	public $clientId;

	/**
	 * @var int
	 */
	public $secretExpirationDate;

	/**
	 * @var KalturaStringArray
	 */
	public $sites;

	/**
	 * @var KalturaStringArray
	 */
	public $drives;

	/**
	 * Associative array, connecting each drive ID with the token for its most recent items.
	 * @var KalturaKeyValueArray
	 */
	public $driveTokens;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'clientSecret',
		'clientId',
		'sites',
		'drives',
		'driveTokens',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new MiscrosoftTeamsIntegration();
		}

		return parent::toObject($dbObject, $skip);
	}
}
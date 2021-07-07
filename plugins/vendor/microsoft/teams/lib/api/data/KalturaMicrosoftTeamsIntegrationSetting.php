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
		'secretExpirationDate',
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

	public function toInsertableObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new MicrosoftTeamsIntegration();
		}
		$dbObject->setVendorType(MicrosoftTeamsDropFolderPlugin::getVendorTypeCoreValue(MicrosoftTeamsVendorType::MS_TEAMS));

		return parent::toInsertableObject($dbObject, $skip);
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		if (!MicrosoftTeamsDropFolderPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		$this->validatePropertyNotNull('clientSecret');
		$this->validatePropertyNotNull('clientId');

		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
}
<?php

/**
 * @package plugins.KTeams
 * @subpackage api.objects
 */
class KalturaTeamsIntegrationSetting extends KalturaIntegrationSetting
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
	 * @var string
	 */
	public $userFilterTag;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'clientSecret',
		'clientId',
		'secretExpirationDate',
		'userFilterTag',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new TeamsIntegration();
		}

		return parent::toObject($dbObject, $skip);
	}

	public function toInsertableObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new TeamsIntegration();
		}
		$dbObject->setVendorType(KTeamsPlugin::getVendorTypeCoreValue(TeamsVendorType::K_TEAMS));

		return parent::toInsertableObject($dbObject, $skip);
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		if (!KTeamsPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		$this->validatePropertyNotNull('clientSecret');
		$this->validatePropertyNotNull('clientId');

		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (!KTeamsPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()) || !KTeamsPlugin::isAllowedPartner($sourceObject->getPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if (!KTeamsPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId())) {
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForUpdate($propertiesToSkip);
	}
}
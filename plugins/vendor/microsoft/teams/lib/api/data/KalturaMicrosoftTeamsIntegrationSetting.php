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
     * User-level custom metadata profile ID which will contain encrypted user-level Graph access data.
     * @var int
     */
	public $userMetadataProfileId;

    /**
     * MS Graph permission scopes for delegate auth
     * @var string
     */
	public $scopes;

    /**
     * Encryption key used for encrypting/decrypting user auth data.
     * @readonly
     * @requiresPermission read
     * @var string
     */
	public $encryptionKey;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'clientSecret',
		'clientId',
        'userMetadataProfileId',
        'scopes',
        'encryptionKey'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new MicrosoftTeamsIntegration();
		}

		return parent::toObject($dbObject, $skip);
	}

	public function toInsertableObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject)) {
			$dbObject = new MicrosoftTeamsIntegration();
		}
		$dbObject->setVendorType(MicrosoftTeamsDropFolderPlugin::getVendorTypeCoreValue(MicrosoftTeamsVendorType::MS_TEAMS));
        $dbObject->setEncryptionKey(md5(KCryptoWrapper::random_pseudo_bytes(16)));

		return parent::toInsertableObject($dbObject, $skip);
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		if (!MicrosoftTeamsDropFolderPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (!MicrosoftTeamsDropFolderPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()) || !MicrosoftTeamsDropFolderPlugin::isAllowedPartner($sourceObject->getPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if (!MicrosoftTeamsDropFolderPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId())) {
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

        $this->validatePropertyNotNull('clientSecret');
        $this->validatePropertyNotNull('clientId');

		parent::validateForUpdate($propertiesToSkip);
	}
}
<?php
/*
* @package plugins.MicrosoftTeamsDropFolder
* @subpackage model
*/
class MicrosoftTeamsIntegration extends VendorIntegration
{
	const CLIENT_SECRET = 'client_secret';
	const CLIENT_ID = 'client_id';
	const USER_METADATA_PROFILE_ID = 'user_metadata_profile_id';
	const ENCRYPTION_KEY = 'encryption_key';
	const SCOPES = 'scopes';

	/**
	 * @return string
	 */
	public function getClientSecret()
	{
		return $this->getFromCustomData(self::CLIENT_SECRET);
	}

	/**
	 * @param string $clientSecret
	 */
	public function setClientSecret($clientSecret)
	{
		$this->putInCustomData(self::CLIENT_SECRET, $clientSecret);
	}

	/**
	 * @return int
	 */
	public function getUserMetadataProfileId()
	{
		return $this->getFromCustomData(self::USER_METADATA_PROFILE_ID);
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setUserMetadataProfileId($metadataProfileId)
	{
		$this->putInCustomData(self::USER_METADATA_PROFILE_ID, $metadataProfileId);
	}

	/**
	 * @return string
	 */
	public function getClientId()
	{
		return $this->getFromCustomData(self::CLIENT_ID);
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId)
	{
		$this->putInCustomData(self::CLIENT_ID, $clientId);
	}

	/**
	 * @return string
	 */
	public function getEncryptionKey()
	{
		return $this->getFromCustomData(self::ENCRYPTION_KEY);
	}

	/**
	 * @param string $key
	 */
	public function setEncryptionKey($key)
	{
		$this->putInCustomData(self::ENCRYPTION_KEY, $key);
	}

	/**
	 * @return string
	 */
	public function getScopes()
	{
		return $this->getFromCustomData(self::SCOPES);
	}

	/**
	 * @param string $scopes
	 */
	public function setScopes($scopes)
	{
		$this->putInCustomData(self::SCOPES, $scopes);
	}
}
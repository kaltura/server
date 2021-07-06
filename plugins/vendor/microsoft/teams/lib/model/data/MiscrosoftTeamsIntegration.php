<?php
/*
* @package plugins.microsoftTeamsDropFolder
* @subpackage model
*/
class MiscrosoftTeamsIntegration extends VendorIntegration
{
	const CLIENT_SECRET = 'client_secret';
	const CLIENT_ID = 'client_id';
	const SITES = 'sites';
	const DRIVES = 'drives';
	const DRIVE_TOKENS = 'drive_tokens';

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
		$this->putInCustomData(self::CLIENT_SECRET);
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
		$this->putInCustomData(self::CLIENT_ID);
	}

	/**
	 * @return array
	 */
	public function getSites()
	{
		return $this->getFromCustomData(self::SITES);
	}

	/**
	 * @param array $sites
	 */
	public function setSites($sites)
	{
		$this->putInCustomData(self::SITES);
	}

	/**
	 * @return array
	 */
	public function getDrives()
	{
		return $this->getFromCustomData(self::DRIVES);
	}

	/**
	 * @param array $drives
	 */
	public function setDrives($drives)
	{
		$this->putInCustomData(self::DRIVES);
	}

	/**
	 * @return array
	 */
	public function getDriveTokens()
	{
		return $this->getFromCustomData(self::DRIVE_TOKENS);
	}

	/**
	 * @param array $driveTokens
	 */
	public function setDriveTokens($driveTokens)
	{
		$this->putInCustomData(self::DRIVE_TOKENS);
	}

}
<?php
/*
* @package plugins.MicrosoftTeamsDropFolder
* @subpackage model
*/
class MicrosoftTeamsIntegration extends VendorIntegration
{
	const CLIENT_SECRET = 'client_secret';
	const CLIENT_ID = 'client_id';
	const SECRET_EXPIRATION_DATE = 'secret_expiration_date';

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
	public function getSecretExpirationDate()
	{
		return $this->getFromCustomData(self::SECRET_EXPIRATION_DATE);
	}

	/**
	 * @param int $secretExpirationDate
	 */
	public function setSecretExpirationDate($secretExpirationDate)
	{
		$this->putInCustomData(self::SECRET_EXPIRATION_DATE, $secretExpirationDate);
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

}
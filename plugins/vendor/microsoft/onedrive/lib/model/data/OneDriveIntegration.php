<?php
/*
* @package plugins.OneDrive
* @subpackage model
*/
class OneDriveIntegration extends VendorIntegration
{
	const CLIENT_SECRET = 'client_secret';
	const CLIENT_ID = 'client_id';
	const SECRET_EXPIRATION_DATE = 'secret_expiration_date';
	const USER_FILTER_TAG = 'user_filter_tag';
	const IS_INITIALIZED = 'is_initialized';

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
	
	/**
	 * @return string
	 */
	public function getUserFilterTag()
	{
		return $this->getFromCustomData(self::USER_FILTER_TAG);
	}
	
	/**
	 * @param string $userFilterTag
	 */
	public function setUserFilterTag($userFilterTag)
	{
		$this->putInCustomData(self::USER_FILTER_TAG, $userFilterTag);
	}
	
	/**
	 * @return bool
	 */
	public function getIsInitialized()
	{
		return $this->getFromCustomData(self::IS_INITIALIZED);
	}
	
	/**
	 * @param string $isInitialized
	 */
	public function setIsInitialized($isInitialized)
	{
		$this->putInCustomData(self::IS_INITIALIZED, $isInitialized);
	}
}
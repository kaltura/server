<?php
class Kaltura_AuthAdapter implements Zend_Auth_Adapter_Interface
{
	/**
	 * @var string
	 */
	protected $username;
	
	/**
	 * @var string
	 */
	protected $password;
	/**
	 * Sets username and password for authentication
	 *
	 * @return void
	 */
	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		if (!$this->username || !$this->password)
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
		
		$settings = Zend_Registry::get('config')->settings;
		$partnerId = $settings->partnerId;
		
		$client = Kaltura_ClientHelper::getClient();
		$client->setKs(null);
		try
		{
			$ks = $client->user->loginByLoginId($this->username, $this->password, $partnerId);
			$client->setKs($ks);
			$user = $client->user->getByLoginId($this->username, $partnerId);
			$identity = new Kaltura_UserIdentity($user, $ks);
			if ($user->partnerId != $partnerId) {
				throw new Exception('SYSTEM_USER_INVALID_CREDENTIALS');
			}
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() === 'SYSTEM_USER_INVALID_CREDENTIALS' || $ex->getCode() === 'SYSTEM_USER_DISABLED' || $ex->getCode() === 'USER_WRONG_PASSWORD' || $ex->getCode() === 'USER_NOT_FOUND')
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
			else
				throw $ex;
		}
	}

}
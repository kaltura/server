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
			
		$client = Kaltura_ClientHelper::getClient();
		$ks = null;
		try
		{
			$systemUser = $client->systemUser->verifyPassword($this->username, $this->password);
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $systemUser);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() === 'SYSTEM_USER_INVALID_CREDENTIALS' || $ex->getCode() === 'SYSTEM_USER_DISABLED')
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
			else
				throw $ex;
		}
	}
}
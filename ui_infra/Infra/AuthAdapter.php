<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AuthAdapter implements Zend_Auth_Adapter_Interface
{
    const SYSTEM_USER_INVALID_CREDENTIALS = 'SYSTEM_USER_INVALID_CREDENTIALS';
    
    const SYSTEM_USER_DISABLED = 'SYSTEM_USER_DISABLED';
    
    const USER_WRONG_PASSWORD = 'USER_WRONG_PASSWORD';
    
    const USER_NOT_FOUND = 'USER_NOT_FOUND';
    
	/**
	 * @var string
	 */
	protected $username;
	
	/**
	 * @var string
	 */
	protected $password;
	
	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @var int
	 */
	protected $timezoneOffset;
	
	/**
	 * @var string
	 */
	protected $privileges = null;
	
	/**
	 * @var string
	 */
	protected $ks;
	
	/**
	 * Sets username and password for authentication
	 */
	public function setCredentials($username, $password = null)
	{
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * Sets ks privileges for authentication
	 */
	public function setPrivileges($privileges)
	{
		$this->privileges = $privileges;
	}
	
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}

	public function setTimezoneOffset($timezoneOffset)
	{
		$this->timezoneOffset = $timezoneOffset;
	}

	public function setKS($ks)
	{
		$this->ks = $ks;
	}
	
	/**
	 * @param Kaltura_Client_Type_User $user
	 * @param string $ks
	 * @param int $partnerId
	 *
	 * @return Infra_UserIdentity
	 */
	protected function getUserIdentity(Kaltura_Client_Type_User $user = null, $ks = null, $partnerId = null)
	{
		return new Infra_UserIdentity($user, $ks, $this->timezoneOffset, $partnerId);
	}
	
	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		// Whether the authntication succeeds or fails - generate a fresh session ID
		// This will assist in preventing session hijacking
		// This will also apply session options and cookie updates (e.g. cookie_secure)
		Zend_Session::regenerateId();

		if($this->ks)
		{
			$client = Infra_ClientHelper::getClient();
			$client->setKs($this->ks);
			
    		$user = $client->user->get();
    		/* @var $user Kaltura_Client_Type_User */
    		$identity = $this->getUserIdentity($user, $this->ks, $user->partnerId);
    		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
		}
		
		if (!$this->username || !$this->password)
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
		
		$partnerId = null;
		$settings = Zend_Registry::get('config')->settings;
		if(isset($settings->partnerId))
			$partnerId = $settings->partnerId;
		
		$client = Infra_ClientHelper::getClient();
		$client->setKs(null);
		
		try
		{
			if ($this->partnerId)
			{
			    $ks = $client->user->loginByLoginId($this->username, $this->password, $this->partnerId, null, $this->privileges);
	    		$client->setKs($ks);
	    		$user = $client->user->getByLoginId($this->username, $this->partnerId);
	    		$identity = $this->getUserIdentity($user, $ks, $this->partnerId);
	    		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
			}
			
		    if (!$this->ks)
    		    $this->ks = $client->user->loginByLoginId($this->username, $this->password, $partnerId, null, $this->privileges);
    		$client->setKs($this->ks);
    		$user = $client->user->getByLoginId($this->username, $partnerId);
    		$identity = $this->getUserIdentity($user, $this->ks, $user->partnerId);
			
			if ($partnerId && $user->partnerId != $partnerId) {
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
			}
			
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() === self::SYSTEM_USER_INVALID_CREDENTIALS || $ex->getCode() === self::SYSTEM_USER_DISABLED || $ex->getCode() === self::USER_WRONG_PASSWORD || $ex->getCode() === self::USER_NOT_FOUND)
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
			else
				throw $ex;
		}
	}

}
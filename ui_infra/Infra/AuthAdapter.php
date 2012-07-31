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
	 * Sets username and password for authentication
	 *
	 * @return void
	 */
	public function __construct($username, $password, $timezoneOffset, $partnerId = null)
	{
		$this->username = $username;
		$this->password = $password;
		$this->timezoneOffset = $timezoneOffset;
		if ($partnerId)
		    $this->partnerId = $partnerId;
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
		
		$client = Infra_ClientHelper::getClient();
		$client->setKs(null);
		
		if ($this->partnerId)
		{
		    $ks = $client->user->loginByLoginId($this->username, $this->password, $this->partnerId);
    		$client->setKs($ks);
    		$user = $client->user->getByLoginId($this->username, $this->partnerId);
    		$identity = new Infra_UserIdentity($user, $ks, $this->timezoneOffset, $this->partnerId, $this->password);
    		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
		}
		
		try
		{
    		$ks = $client->user->loginByLoginId($this->username, $this->password, $partnerId);
    		$client->setKs($ks);
    		$user = $client->user->getByLoginId($this->username, $partnerId);
    		$identity = new Infra_UserIdentity($user, $ks, $this->timezoneOffset, $partnerId, $this->password);
			//New logic - if specific permissions are required to authenticate the partner/user, check their existence here.
    		
    		if (isset($settings->requiredPermissions) && $settings->requiredPermissions)
    		{
    		    $requiredPermissionsArr = explode(",", $settings->requiredPermissions);

			    foreach ($requiredPermissionsArr as $requiredPermission)
			    {
			        $permissionFilter = new Kaltura_Client_Type_PermissionFilter();
			        $permissionFilter->nameEqual = $requiredPermission;
			        $permissionFilter->statusEqual = Kaltura_Client_Enum_PermissionStatus::ACTIVE;
			        $permissions = $client->permission->listAction($permissionFilter, new Kaltura_Client_Type_FilterPager());
			        if (!$permissions->totalCount)
			        {
			            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
			        }
			    }
    		}
    		
			
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
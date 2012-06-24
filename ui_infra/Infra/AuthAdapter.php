<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AuthAdapter implements Zend_Auth_Adapter_Interface
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
	public function __construct($username, $password, $timezoneOffset)
	{
		$this->username = $username;
		$this->password = $password;
		$this->timezoneOffset = $timezoneOffset;
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
		
		$requiredPermissions= $settings->requiredPermissions;
		
		$client = Infra_ClientHelper::getClient();
		$client->setKs(null);
		
		$ks = $client->user->loginByLoginId($this->username, $this->password, $this->partnerId);
		$client->setKs($ks);
		$user = $client->user->getByLoginId($this->username, $partnerId);

		
		try
		{
			//New logic - if specific permissions are required to authenticate the partner/user, check their existence here.
    		$requiredPermissionsArr = explode(",", $requiredPermissions);
    		if ($requiredPermissionsArr && count($requiredPermissionsArr))
    		{
    			$userPartners = $client->partner->listPartnersForUser();
    			
    			$authorizedPartnerId = null;
    			foreach ($userPartners as $userPartner)
    			{
    			    $authorizedPartnerId = $userPartner->id;
    			    foreach ($requiredPermissionsArr as $requiredPermission)
    			    {
    			        $permissionFilter = new Kaltura_Client_Type_PermissionFilter();
    			        $permissionFilter->nameEqual = $requiredPermission;
    			        $permissionFilter->partnerIdEqual = $userPartner->id;
    			        $permissions = $client->permission->listAction($permissionFilter, new Kaltura_Client_Type_FilterPager());
    			        if (!$permissions->totalCount)
    			        {
    			            $authorizedPartnerId = null;
    			            break;
    			        }
    			    }
    			    
    			}
    			
    			if (!$authorizedPartnerId)
    			{
    			    throw new Exception('SYSTEM_USER_INVALID_CREDENTIALS');
    			}
    			
    		    $ks = $client->user->loginByLoginId($this->username, $this->password, $authorizedPartnerId);
    			$client->setKs($ks);
    			$user = $client->user->getByLoginId($this->username, $authorizedPartnerId);
    		}
    		
			$identity = new Infra_UserIdentity($user, $ks, $this->timezoneOffset);
			if ($partnerId && $user->partnerId != $partnerId) {
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
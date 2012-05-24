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
		$authorizedPartnerId = $settings->partnerId;
		
		$requiredPermissions= $settings->requiredPermissions;
		
		$client = Infra_ClientHelper::getClient();
		$client->setKs(null);
		try
		{
			$ks = $client->user->loginByLoginId($this->username, $this->password, $authorizedPartnerId);
			$client->setKs($ks);
			$user = $client->user->getByLoginId($this->username, $authorizedPartnerId);
			$identity = new Infra_UserIdentity($user, $ks);
			if ($authorizedPartnerId && $user->partnerId != $authorizedPartnerId) {
				throw new Exception('SYSTEM_USER_INVALID_CREDENTIALS');
			}

			//New logic - if specific permissions are required to authenticate the partner/user, check their existence here.
//			$requiredPermissionsArr = explode(",", $requiredPermissions);
//			foreach ($requiredPermissionsArr as $requiredPermission)
//			{
//			    $filter = new Kaltura_Client_Type_PermissionFilter();
//			    $filter->nameEqual = $requiredPermission;
//			    $existingPermissions = $client->permission->listAction($filter);
//			    KalturaLog::debug("permissions total count: ".$existingPermissions->totalCount);
//			    if ( !$existingPermissions->totalCount )
//			    {
//			        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
//			    }
//			}
			
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
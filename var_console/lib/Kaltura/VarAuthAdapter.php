<?php
/**
 * @package Var
 * @subpackage Authentication
 */
class Kaltura_VarAuthAdapter extends Infra_AuthAdapter
{
	/* (non-PHPdoc)
	 * @see Infra_AuthAdapter::getUserIdentity()
	 */
	protected function getUserIdentity(Kaltura_Client_Type_User $user = null, $ks = null, $partnerId = null)
	{
		$identity = new Kaltura_VarUserIdentity($user, $ks, $this->timezoneOffset, $partnerId);
		$identity->setPassword($this->password);
		
		return $identity;
	}
	
	/* (non-PHPdoc)
	 * @see Infra_AuthAdapter::authenticate()
	 */
	public function authenticate()
	{
		$result = parent::authenticate();
		if($result->getCode() != Zend_Auth_Result::SUCCESS)
			return $result;
			
		$identity = $result->getIdentity();
		if(!($identity instanceof Kaltura_VarUserIdentity))
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, null, array('Identity is not a multi-publisher identity'));
			
		$client = Infra_ClientHelper::getClient();
		$client->setKs($identity->getKs());
		
		$settings = Zend_Registry::get('config')->settings;
		
		try
		{
    		if (isset($settings->requiredPermissions) && $settings->requiredPermissions)
    		{
    		    $requiredPermissionsArr = explode(",", $settings->requiredPermissions);
    		    
    		    $hasRequiredPermissions = true;
    		    foreach ($requiredPermissionsArr as $requiredPermission)
			    {
			        $permissionFilter = new Kaltura_Client_Type_PermissionFilter();
			        $permissionFilter->nameEqual = $requiredPermission;
			        $permissionFilter->statusEqual = Kaltura_Client_Enum_PermissionStatus::ACTIVE;
			        $permissions = $client->permission->listAction($permissionFilter, new Kaltura_Client_Type_FilterPager());
			        if (!$permissions->totalCount)
			        {
			            $hasRequiredPermissions = false;
			            break;
			        }
			    }
		    
			    if (!$hasRequiredPermissions)
			    {
			        $filter = new Kaltura_Client_VarConsole_Type_VarConsolePartnerFilter();
    		        $filter->partnerPermissionsExist = $settings->requiredPermissions;
    		        $filter->groupTypeIn = Kaltura_Client_Enum_PartnerGroupType::GROUP . "," . Kaltura_Client_Enum_PartnerGroupType::VAR_GROUP;
        		    
        			$userPartners = $client->partner->listPartnersForUser($filter);
        			
        			if (!$userPartners->totalCount)
        			    return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
        			
        			$authorizedPartnerId = $userPartners->objects[0]->id;
        			
        			$client->setKs(null);
        		    $ks = $client->user->loginByLoginId($this->username, $this->password, $authorizedPartnerId);
        			$client->setKs($ks);
        			$user = $client->user->getByLoginId($this->username, $authorizedPartnerId);
        			$identity = $this->getUserIdentity($user, $ks, $authorizedPartnerId);
			    }
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
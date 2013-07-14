<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AclHelper
{
	const ROLE_GUEST = 'guest';
	
	/**
	 * @return string
	 */
	public static function getCurrentRole()
	{
		if(Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			$roleIds = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->roleIds;
			
			return $roleIds;
		}
		return self::ROLE_GUEST;
	}
	
	public static function getCurrentPermissions()
	{
		if(Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			$permissions = Infra_AuthHelper::getAuthInstance()->getIdentity()->getPermissions();
			return $permissions;
		}
		return array();
	}
	
	/**
	 *
	 * @param  Zend_Acl_Resource_Interface|string $resource
	 * @param  string                             $privilege
	 * @return boolean
	 */
	public static function isAllowed($resource, $privilege)
	{
		if(!$resource)
			return false;
		
		$acl = Zend_Registry::get('acl');
		if(!$acl->has($resource))
		{
			KalturaLog::info("Resource [$resource] privilege [$privilege] not found!");
			return false;
		}
		$allowed = $acl->isAllowed(self::getCurrentRole(), $resource, $privilege);
		return $allowed;
	}
	
	/**
	 *
	 * @param  Zend_Acl_Resource_Interface|string $resource
	 * @param  string                             $privilege
	 * @return boolean
	 */
	public static function validateAccess($resource, $privilege)
	{
		if(!self::isAllowed($resource, $privilege))
		{
			$message = "Access denied to resource[$resource], needed privilege [$privilege]";
			KalturaLog::err($message);
			throw new Infra_Exception($message, Infra_Exception::ERROR_CODE_ACCESS_DENIED);
		}
	}
}
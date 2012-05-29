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
		if (Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			$roleIds = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->roleIds;
			
			return $roleIds;
		}
		return self::ROLE_GUEST;
	}
	
	public static function getCurrentPermissions()
	{
		if (Infra_AuthHelper::getAuthInstance()->hasIdentity())
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
		if (!$acl->has($resource)) 
		{
			KalturaLog::err("Resource [$resource] privilege [$privilege] not found!");
			return false;
		}
		$allowed = $acl->isAllowed(self::getCurrentRole(), $resource, $privilege);
		return $allowed;
	}
	
	public static function isAllowedPartner($partnerId, $partnerPackage) {
		if (Infra_AuthHelper::getAuthInstance()->hasIdentity()) 
		{
			$partners = Infra_AuthHelper::getAuthInstance()->getIdentity()->getAllowedPartners();
			if (in_array('*', $partners)) {
				return true;
			}
			$packages = Infra_AuthHelper::getAuthInstance()->getIdentity()->getAllowedPartnerPackages();
			if (in_array($partnerPackage, $packages)) {
				return true;
			}			
			return in_array((string)$partnerId, $partners);
		}
		return false;
	}
	
	/**
	 * Refresh the list of partners the user is allowed to access.
	 */
	public static function refreshCurrentUserAllowrdPartners() {
		if (Infra_AuthHelper::getAuthInstance()->hasIdentity()) 
		{
			Infra_AuthHelper::getAuthInstance()->getIdentity()->refreshAllowedPartners();
		}
	}
	
	
	
	/**
	 * 
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @return boolean
     */
	public static function validateAccess($resource, $privilege)
	{
		if (!self::isAllowed($resource, $privilege))
		{
			throw new Exception('Access denied '.$resource.'-'.$privilege);
		}
	}
}
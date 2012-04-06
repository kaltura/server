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
		if (Zend_Auth::getInstance()->hasIdentity())
		{
			$roleIds = Zend_Auth::getInstance()->getIdentity()->getUser()->roleIds;
			return $roleIds;
		}
		return self::ROLE_GUEST;
	}
	
	public static function getCurrentPermissions()
	{
		if (Zend_Auth::getInstance()->hasIdentity())
		{
			$permissions = Zend_Auth::getInstance()->getIdentity()->getPermissions();
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
		$acl = Zend_Registry::get('acl');
		if (!$acl->has($resource)) {
			KalturaLog::err("Resource [$resource] privilege [$privilege] not found!");
			return false;
		}
		$allowed = $acl->isAllowed(self::getCurrentRole(), $resource, $privilege);
		return $allowed;
	}
	
	public static function isAllowedPartner($partnerId, $partnerPackage) {
		if (Zend_Auth::getInstance()->hasIdentity()) 
		{
			$partners = Zend_Auth::getInstance()->getIdentity()->getAllowedPartners();
			if (in_array('*', $partners)) {
				return true;
			}
			$packages = Zend_Auth::getInstance()->getIdentity()->getAllowedPartnerPackages();
			if (in_array($partnerPackage, $packages)) {
				return true;
			}			
			return in_array((string)$partnerId, $partners);
		}
		return false;
	}
	
	public static function refreshCurrentUserAllowrdPartners() {
		if (Zend_Auth::getInstance()->hasIdentity()) 
		{
			Zend_Auth::getInstance()->getIdentity()->refreshAllowedPartners();
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
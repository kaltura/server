<?php
class Kaltura_AclHelper
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
		if (!self::isAllowed($resource, $privilege))
		{
			throw new Exception('Access denied '.$resource.'-'.$privilege);
		}
	}
}
<?php
class Kaltura_AclHelper
{
	const ROLE_GUEST = 'guest';
	const ROLE_PROFESIONAL_SERVICES = 'ps';
	const ROLE_ADMINISTRATOR = 'admin';
	
	/**
	 * @return string
	 */
	public static function getCurrentRole()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$partnerData = unserialize(Zend_Auth::getInstance()->getIdentity()->getUser()->partnerData);
			if (isset($partnerData->role) && $partnerData->role)
				return $partnerData->role;
			else
				return self::ROLE_GUEST;
		}
		else
			return self::ROLE_GUEST;
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
		return $acl->isAllowed(self::getCurrentRole(), $resource, $privilege);
	}
	
	/**
	 * 
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @return boolean
     */
	public static function validateAccess($resouce, $privilege)
	{
		if (!self::isAllowed($resouce, $privilege))
		{
			throw new Exception('Access denied');
		}
	}
}
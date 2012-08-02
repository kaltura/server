<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AuthHelper
{
    static public function getAuthInstance ()
    {
        $settings = Zend_Registry::get("config")->settings;
        $defNameSpace = isset($settings->cookieNameSpace) ? $settings->cookieNameSpace : Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
        
        if ($settings->sessionSavePath)
            session_save_path($settings->sessionSavePath);
        
        $auth = Zend_Auth::getInstance();
		$storage = new Zend_Auth_Storage_Session($defNameSpace);
		$auth->setStorage($storage);
		
		return $auth;
    }
}
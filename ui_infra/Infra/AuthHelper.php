<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AuthHelper
{
	/**
	 * Indicates that the namespace was already defined
	 * @var unknown_type
	 */
	private static $defined = false;
	
	
	/**
	 * array of callbacks to be called if namespace changed
	 * @var array
	 */
	private static $nameSpaceChangedCallbacks = array();
	
    /**
     * @return Zend_Auth
     */
    static public function getAuthInstance ()
    {
    	if(!self::$defined)
    		self::setNamespace();
    		
        return Zend_Auth::getInstance();
    }

    /**
     * Register a callback to be called if namespace changed
     * @param callable $callback
     * @param string $name
     */
    static public function registerNamespaceChangedCallback($callback, $name = null)
    {
    	self::$nameSpaceChangedCallbacks[$name] = $callback;
    }

    /**
     * @param string $namespace
     */
    static public function setNamespace ($namespace = null)
    {
    	self::$defined = true;
    	
        $settings = Zend_Registry::get("config")->settings;
        if(!$namespace)
        	$namespace = isset($settings->cookieNameSpace) ? $settings->cookieNameSpace : Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
        
        if ($settings->sessionSavePath)
            session_save_path($settings->sessionSavePath);
        
        $auth = Zend_Auth::getInstance();
		$storage = new Zend_Auth_Storage_Session($namespace);
		$auth->setStorage($storage);
		
		foreach(self::$nameSpaceChangedCallbacks as $callback)
			call_user_func($callback, $callback);
    }
}
<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_AuthPlugin extends Zend_Controller_Plugin_Abstract 
{
	private static $_whitelist = array(
			'error/error', 
			'error/denied', 
			'user/login', 
			'user/reset-password', 
			'user/reset-password-link', 
			'user/reset-password-ok',
			
		);
	
	public function __construct() 
	{
		
	}
	
	/**
	 * Add a certain URL to the whitelist
	 * @param string $url
	 */
	public static function addToWhitelist ($url)
	{
	    self::$_whitelist[] = $url;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$controller = strtolower($request->getControllerName());
		$action = strtolower($request->getActionName());
		$route = $controller . '/' . $action;
		
		if (in_array($route, self::$_whitelist)) {
			return;
		}
		
		$auth = Infra_AuthHelper::getAuthInstance();
		if ($auth->hasIdentity()) {
			return;
		}
		
		$request->setDispatched(false);
		$request->setControllerName('user');
		$request->setActionName('login');
		$request->setParam('next_uri', $request->getPathInfo());
	}
}
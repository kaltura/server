<?php
class Kaltura_AuthPlugin extends Zend_Controller_Plugin_Abstract 
{
	private $_whitelist;
	
	public function __construct() 
	{
		$this->_whitelist = array(
			'error/error', 
			'error/denied', 
			'user/login', 
			'user/reset-password', 
			'user/reset-password-link', 
			'user/reset-password-ok'
		);
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$controller = strtolower($request->getControllerName());
		$action = strtolower($request->getActionName());
		$route = $controller . '/' . $action;
		
		if (in_array($route, $this->_whitelist)) {
			return;
		}
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			return;
		}
		
		$request->setDispatched(false);
		$request->setControllerName('user');
		$request->setActionName('login');
		$request->setParam('next_uri', $request->getPathInfo());
	}
}
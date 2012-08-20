<?php
/**
 * @package Admin
 */
class PluginController extends Zend_Controller_Action
{
	public function indexAction()
	{
	}

	public function __call($method, $args)
	{
		KalturaLog::debug("Called method [$method] with args [" . print_r($args, true) . "]");
		
		$arr = null;
		if(!preg_match('/^(.+)Action$/', $method, $arr))
			return parent::__call($method, $args);
			
		$class = $arr[1];
		$actionController = new $class();
		if($actionController && $actionController instanceof KalturaAdminConsolePlugin)
			$actionController->action($this);
	}
}
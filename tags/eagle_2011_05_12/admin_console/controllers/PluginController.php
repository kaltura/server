<?php
class PluginController extends Zend_Controller_Action
{
	public function indexAction()
	{
	}

	public function __call($method, $args)
	{
		$arr = null;
		if(!preg_match('/^(.+)Action$/', $method, $arr))
			return parent::__call($method, $args);
			
		$class = $arr[1];
		$actionController = new $class();
		if($actionController && $actionController instanceof KalturaAdminConsolePlugin)
			$actionController->action($this);
	}
}
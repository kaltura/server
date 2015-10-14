<?php
/**
 * @package UI-infra
 * @subpackage Plugins
 */
class Infra_PluginController extends Zend_Controller_Action
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
		if($actionController && $actionController instanceof KalturaApplicationPlugin)
			$actionController->action($this);
	}
	
	public function imgAction()
	{
		$pluginName = $this->_getParam('plugin');
		$imgName = $this->_getParam('img');
		
		$plugin = KalturaPluginManager::getPluginInstance($pluginName);
		if(!$plugin || !($plugin instanceof IKalturaApplicationImages))
		{
			$message = "Plugin [$pluginName] is not an application images plugin";
			throw new Infra_Exception($message, Infra_Exception::ERROR_CODE_MISSING_PLUGIN);
		}
		
		$imgPath = $plugin->getImagePath($imgName);
		if(!file_exists($imgPath))
		{
			$message = "File [$imgPath] not found";
			throw new Infra_Exception($message, Infra_Exception::ERROR_CODE_MISSING_PLUGIN_FILE);
		}
		
		$this->getHelper('layout')->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
		
		header('Content-type: image/jpg');
		
		readfile($imgPath);
	}
}
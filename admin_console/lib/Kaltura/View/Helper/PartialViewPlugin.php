<?php
/**
 * @package Admin
 * @subpackage views
 */
abstract class Kaltura_View_Helper_PartialViewPlugin
{
	/**
	 * Assign the plugin to the view
	 * 
	 * @param Zend_View_Interface $view
	 */
	public function plug(Zend_View_Interface $view)
	{
		$view->addBasePath($this->getTemplatePath());
		if(!isset($view->plugins))
			$view->plugins = array();
			
		$view->plugins[$this->getPHTML()] = $this->getDataArray();	
	}
	
	/**
	 * @return array array of data to be used in the phtml template
	 */
	abstract protected function getDataArray();
	
	/**
	 * @return string the name of the phtml file
	 */
	abstract protected function getPHTML();
	
	/**
	 * @return string the path to the phtml templates folder
	 */
	abstract protected function getTemplatePath();
}
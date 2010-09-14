<?php

class MetadataObjectsAction extends KalturaAdminConsolePlugin
{
	public function __construct($label = null, $action = null, $rootLabel = null)
	{
		$this->action = $action;
		$this->label = $label;
		$this->rootLabel = $rootLabel;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRole()
	{
		return Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		
	}
}
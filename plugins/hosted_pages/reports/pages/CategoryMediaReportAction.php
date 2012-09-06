<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class CategoryMediaReportAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'CategoryMediaReportAction';
		$this->label = 'Category Media';
		$this->rootLabel = 'Reports';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getRequiredPermissions()
	 */
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::USER_SESSION_PERMISSION);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::doAction()
	 */
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
	}
}


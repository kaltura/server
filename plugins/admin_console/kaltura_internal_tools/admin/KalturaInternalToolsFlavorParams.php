<?php
/**
 * @package plugins.KalturaInternalTools
 * @subpackage admin
 */
class KalturaInternalToolsPluginFlavorParams extends KalturaApplicationPlugin
{
	
	public function __construct()
	{
		$this->action = 'KalturaInternalToolsPluginFlavorParams';
		$this->label = 'Flavor Params';
		$this->rootLabel = 'Developer';

	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_INTERNAL);
	}

	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();

        $action->view->form = new Form_NewFlavorParam();
	}
	
	private static function formatThisData ( $time )
	{
		return strftime( "%d/%m %H:%M:%S" , $time );	
	}
}


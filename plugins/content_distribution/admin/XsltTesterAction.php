<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class XsltTesterAction extends KalturaApplicationPlugin
{
    protected $client;

	public function __construct()
	{
		$this->action = 'listDistributionProfiles';
		$this->label = null;
		$this->rootLabel = null;
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
        $entryId = $action->getRequest()->getParam('entry-id');
        $this->client = Infra_ClientHelper::getClient();
        $action->getHelper('layout')->setLayout('layout_empty');
        $action->view->entryId = $entryId;
        $action->view->xml = $this->client->media->getMrss($entryId);
	}
}


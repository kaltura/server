<?php
class GenericDistributionProviderDeleteAction extends KalturaAdminConsolePlugin
{
	public function __construct()
	{
		$this->action = 'deleteGenericDistributionProvider';
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
		return Kaltura_AclHelper::ROLE_ADMINISTRATOR;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
//		$action->_helper->viewRenderer->setNoRender();
		$action->getHelper('viewRenderer')->setNoRender();
		$providerId = $this->_getParam('provider_id');
		$client = Kaltura_ClientHelper::getClient();
		
		try
		{
			$client->genericDistributionProvider->delete($providerId);
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
	}
}


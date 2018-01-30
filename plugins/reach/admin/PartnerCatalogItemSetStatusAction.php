<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnerCatalogItemSetStatusAction extends KalturaApplicationPlugin
{
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$catalogItemId = $this->_getParam('catalogItemId');
		$newStatus = $this->_getParam('catalogItemStatus');
		$partnerId = $this->_getParam('partnerId');

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
		Infra_ClientHelper::impersonate($partnerId);
		try
		{
			if  ( $newStatus == Kaltura_Client_Reach_Enum_VendorCatalogItemStatus::DELETED )
				$res = $reachPluginClient->PartnerCatalogItem->delete($catalogItemId);
			else
				KalturaLog::err("Error trying to set invalid partner catalog item status of [$newStatus]");
			echo $action->getHelper('json')->sendJson('ok', false);
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
		Infra_ClientHelper::unimpersonate();
	}
}
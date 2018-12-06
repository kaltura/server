<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnerCatalogItemsCloneAction extends KalturaApplicationPlugin
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
		$fromPartnerId = $this->_getParam('fromPartnerId');
		$toPartnerId = $this->_getParam('toPartnerId');

		$client = Infra_ClientHelper::getClient();

		try
		{
			$partnerCatalogItems = $this->getPartnerCatalogItems($fromPartnerId);
			Infra_ClientHelper::impersonate($toPartnerId);
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
			$client->startMultiRequest();
			foreach ($partnerCatalogItems as $partnerCatalogItem)
			{
				$partnerCatalogItem = $reachPluginClient->PartnerCatalogItem->add($partnerCatalogItem->id);
			}

			$result = $client->doMultiRequest();
			$resultMessage = null;
			foreach ($result as $resultItem)
			{
				if ($resultItem instanceof Kaltura_Client_Exception)
				{
					if ($resultItem->getCode() == "SERVICE_FORBIDDEN_CONTENT_BLOCKED")
					{
						$resultMessage = $resultItem->getMessage(). ". ";
						break;
					}
					else
						$resultMessage .= $resultItem->getMessage(). ". ";
				}
			}
			if (count($resultMessage))
			{
				echo $action->getHelper('json')->sendJson($resultMessage, false);
			}
			else
			{
				echo $action->getHelper('json')->sendJson('ok', false);
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
		Infra_ClientHelper::unimpersonate();
	}

	protected function getPartnerCatalogItems($partnerId = null)
	{
		Infra_ClientHelper::unimpersonate();// to get all catalog items from partner 0

		$catalogItemProfileFilter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
		$catalogItemProfileFilter->orderBy = "-createdAt";
		$catalogItemProfileFilter->partnerIdEqual = $partnerId;

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
		Infra_ClientHelper::impersonate($partnerId);

		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;

		$partnerCatalogItems = array();
		$result = $reachPluginClient->vendorCatalogItem->listAction($catalogItemProfileFilter, $pager);
		$totalCount = $result->totalCount;
		while ($totalCount > 0)
		{
			foreach ($result->objects as $partnerCatalogItem)
			{
				/* @var $partnerCatalogItem Kaltura_Client_Reach_Type_VendorCatalogItem */
				$partnerCatalogItems[] = $partnerCatalogItem;
			}
			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$result = $reachPluginClient->vendorCatalogItem->listAction($catalogItemProfileFilter, $pager);
		}
		return $partnerCatalogItems;
	}
}
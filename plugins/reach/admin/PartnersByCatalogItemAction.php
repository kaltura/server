<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnersByCatalogItemAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'PartnersByCatalogItemAction';
		$this->label = "Find Partners By Catalog Item";
		$this->rootLabel = "Reach";
	}

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();

		$partnerCatalogItemFilter = new Kaltura_Client_Reach_Type_PartnerCatalogItemFilter;
		$partnerCatalogItemFilter->catalogItemIdEqual = $request->getParam('filter_input') ?? null;

		$action->view->allowed = $this->isAllowedForPartner(null);

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->PartnerCatalogItem, "listAction", null, $partnerCatalogItemFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$partnersByCatalogItemFilterForm = new Form_PartnersByCatalogItemFilter();
		$partnersByCatalogItemFilterForm->populate($request->getParams());
		$partnersByCatalogItemFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$partnersByCatalogItemFilterForm->setAction($partnersByCatalogItemFilterFormAction);

		$action->view->filterForm = $partnersByCatalogItemFilterForm;
		$action->view->paginator = $paginator;
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}

	public function isAllowedForPartner($partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		$client->setPartnerId($partnerId);
		$filter = new Kaltura_Client_Type_PermissionFilter();
		$filter->nameEqual = Kaltura_Client_Enum_PermissionName::REACH_PLUGIN_PERMISSION;
		$filter->partnerIdEqual = $partnerId;
		try
		{
			$result = $client->permission->listAction($filter, null);
		} catch (Exception $e)
		{
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}
}

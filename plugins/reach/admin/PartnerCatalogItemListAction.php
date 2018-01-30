<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnerCatalogItemListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'PartnerCatalogItemListAction';
		$this->label = "Partner Catalog Items";
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
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('filter_input') ? $this->_getParam('filter_input') : $request->getParam('partnerId');
		$ServiceFeature = $this->_getParam('serviceFeature') != "" ? $this->_getParam('serviceFeature') : null;
		$ServiceType = $this->_getParam('serviceType') != "" ? $this->_getParam('serviceType') : null;
		$turnAround = $this->_getParam('turnAround') != "" ? $this->_getParam('turnAround') : null;

		$action->view->allowed = $this->isAllowedForPartner($partnerId);
		if ($partnerId)
		{
			$vendorCatalogItemFilter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
			$vendorCatalogItemFilter->orderBy = "-createdAt";
			$vendorCatalogItemFilter->serviceFeatureEqual = $ServiceFeature;
			$vendorCatalogItemFilter->serviceTypeEqual = $ServiceType;
			$vendorCatalogItemFilter->turnAroundTimeEqual = $turnAround;
			$vendorCatalogItemFilter->partnerIdEqual = $partnerId;

			$client = Infra_ClientHelper::getClient();
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

			// get results and paginate
			Infra_ClientHelper::unimpersonate();
			$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", $partnerId, $vendorCatalogItemFilter);

			// init filter
			$paginator = new Infra_Paginator($paginatorAdapter, $request);
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage($pageSize);
			$action->view->paginator = $paginator;
		}

		// set view
		$catalogItemProfileFilterForm = new Form_PartnerCatalogItemFilter();
		$catalogItemProfileFilterForm->populate($request->getParams());
		$catalogItemProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$catalogItemProfileFilterForm->setAction($catalogItemProfileFilterFormAction);

		$action->view->filterForm = $catalogItemProfileFilterForm;

		$createProfileForm = new Form_PartnerCreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'PartnerCatalogItemConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);

		$action->view->newPartnerCatalogItemFolderForm = $createProfileForm;
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
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}
}
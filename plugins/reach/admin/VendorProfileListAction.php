<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class VendorProfileListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";
	
	public function __construct()
	{
		$this->action = 'VendorProfileListAction';
		$this->label = "VendorProfileList";
		$this->rootLabel = "Reach";
	}

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

//	public function getRequiredPermissions()
//	{
//		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CATALOG_ITEM_BASE);
//	}

	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('filter_input');
		
		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		// init filter
		$catalogItemProfileFilter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
		$catalogItemProfileFilter->orderBy = "-createdAt";

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", $partnerId, $catalogItemProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$catalogItemProfileFilterForm = new Form_CatalogItemFilter();
		$catalogItemProfileFilterForm->populate($request->getParams());
		$catalogItemProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$catalogItemProfileFilterForm->setAction($catalogItemProfileFilterFormAction);

		$action->view->filterForm = $catalogItemProfileFilterForm;
		$action->view->paginator = $paginator;

		$createProfileForm = new Form_CreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'CatalogItemConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);

		if ($partnerId)
			$createProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newCatalogItemFolderForm = $createProfileForm;

		// get results and paginate
		$listCatalogItemTemplatespager = new Kaltura_Client_Type_FilterPager();
		$listCatalogItemTemplatespager->pageSize = 500;
		$templatesList = $reachPluginClient->vendorCatalogItem->listTemplates(null, $listCatalogItemTemplatespager);

		$templates = array();
		foreach($templatesList->objects as $template)
		{
			$obj = new stdClass();
			$obj->id = $template->id;
			$obj->systemName = $template->systemName;
			$obj->serviceFeature = $template->serviceFeature; // Caption or Translation
			$obj->serviceType = $template->serviceType; // Human Or machine
			$obj->turnAroundTime = $template->turnAroundTime; // TurnAroundTime
			$obj->name = $template->name;
			$templates[] = $obj;
		}

		$action->view->templates = $templates;
	}

	private function getCatalogItemProfileFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
		$filterInput = $request->getParam('filter_input');
		if (!strlen($filterInput))
			return $filter;

		$filterType = $request->getParam('filter_type');
		$filter->$filterType = $filterInput;

		return $filter;
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
		$result = $client->permission->listAction($filter, null);
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
		
		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}
}
<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'CatalogItemProfileListAction';
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

//	public function getRequiredPermissions()
//	{
//		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CATALOG_ITEM_BASE);
//	}

	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();

		$partnerId = $this->_getParam('filter_input');

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate for catalogItems
		$this->loadCatalogItemsList($action, $reachPluginClient, $partnerId, $request);

		// get results and paginate for vendorProfile
		$this->loadVendorProfilesList($action, $reachPluginClient, $partnerId, $request);

		$this->loadCreateCatalogItemForm($action, $partnerId);

		$this->loadCreateVendorProfileForm($action, $partnerId);

		$this->loadVendorCatalogItemsTemplates($action, $reachPluginClient);

	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $reachPluginClient
	 * @param $partnerId
	 * @param $request
	 */
	protected function loadCatalogItemsList(Zend_Controller_Action $action, $reachPluginClient, $partnerId, $request)
	{
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		// init filter
		$catalogItemProfileFilter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
		$catalogItemProfileFilter->orderBy = "-createdAt";

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
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $reachPluginClient
	 * @param $partnerId
	 * @param $request
	 */
	protected function loadVendorProfilesList(Zend_Controller_Action $action, $reachPluginClient, $partnerId, $request)
	{
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		// init filter
		$vendorProfileFilter = new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
		$vendorProfileFilter->orderBy = "-createdAt";

		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", $partnerId, $vendorProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$vendorProfileFilterForm = new Form_CatalogItemFilter();
		$vendorProfileFilterForm->populate($request->getParams());
		$vendorProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$vendorProfileFilterForm->setAction($vendorProfileFilterFormAction);

		$action->view->vendorProfilefilterForm = $vendorProfileFilterForm;
		$action->view->vendorProfilePaginator = $paginator;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 */
	protected function loadCreateCatalogItemForm(Zend_Controller_Action $action, $partnerId)
	{
		$createCatalogItemForm = new Form_CreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'CatalogItemConfigure'), null, true);
		$createCatalogItemForm->setAction($actionUrl);

		if ($partnerId)
			$createCatalogItemForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newCatalogItemFolderForm = $createCatalogItemForm;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 */
	protected function loadCreateVendorProfileForm(Zend_Controller_Action $action, $partnerId)
	{
		$createProfileForm = new Form_CreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'VendorProfileConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);

		if ($partnerId)
			$createProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->vendorProfileFolderForm = $createProfileForm;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $reachPluginClient
	 */
	protected function loadVendorCatalogItemsTemplates(Zend_Controller_Action $action, $reachPluginClient)
	{
// get results and paginate
		$listCatalogItemTemplatespager = new Kaltura_Client_Type_FilterPager();
		$listCatalogItemTemplatespager->pageSize = 500;
		$templatesList = $reachPluginClient->vendorCatalogItem->listTemplates(null, $listCatalogItemTemplatespager);

		$templates = array();
		foreach ($templatesList->objects as $template)
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

	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array(0 => 'Catalog Items', 1 => 'listCatalogItems');
		return $options;

	}

	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listCatalogItems(partnerId) {
			var url = pluginControllerUrl + \'/' . get_class($this) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
			document.location = url;
		}';
		return $functionStr;
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}
}
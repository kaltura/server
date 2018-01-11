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
		$this->label = "Vendor Profiles";
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

		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		// init filter
		$vendorProfileFilter = new Kaltura_Client_Reach_Type_VendorProfileFilter();
		$vendorProfileFilter->orderBy = "-createdAt";

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorProfile, "listAction", $partnerId, $vendorProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$vendorProfileFilterForm = new Form_VendorProfileFilter();
		$vendorProfileFilterForm->populate($request->getParams());
		$vendorProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$vendorProfileFilterForm->setAction($vendorProfileFilterFormAction);

		$action->view->filterForm = $vendorProfileFilterForm;
		$action->view->paginator = $paginator;

		$createVendorProfileForm = new Form_CreateVendorProfile();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'VendorProfileConfigure'), null, true);
		$createVendorProfileForm->setAction($actionUrl);

		if ($partnerId)
			$createVendorProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newVendorProfileFolderForm = $createVendorProfileForm;

	}

	private function getCatalogItemProfileFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Reach_Type_VendorProfileFilter();
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
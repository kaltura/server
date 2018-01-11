<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'CatalogItemListAction';
		$this->label = "Catalog Items";
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
		$templates = array();
		try
		{
			$templatesList = $reachPluginClient->vendorCatalogItem->listTemplates(null, $listCatalogItemTemplatespager);


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
		} catch (Exception $e)
		{
			// List call failed since we might be using a non existing partner. we will return an empty list.
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
		$options[] = array(0 => 'Reach', 1 => 'listCatalogItems');
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
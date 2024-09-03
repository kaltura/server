<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachRequestsListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = '-2';

	public function __construct()
	{
		$this->action = 'ReachRequestsListAction';
		$this->label = 'Reach Requests';
		$this->rootLabel = 'Reach';
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
		$partnerId = null;
		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		$entryVendorTaskFilter = $this->initFilter($request);
		$paginator = self::getPaginator($reachPluginClient, $partnerId, $entryVendorTaskFilter, $request, $page, $pageSize);
		$entryVendorTaskFilterForm = self::getFilterForm($request, $action);
		$action->view->filterForm = $entryVendorTaskFilterForm;
		$action->view->paginator = $paginator;

		$this->addCreatedAtParamsToFilter($request, $action, $entryVendorTaskFilter);
	}

	protected function initFilter($request)
	{
		$entryVendorTaskFilter = $this->getEntryVendorTaskFilter($request);
		$entryVendorTaskFilter->orderBy = '-createdAt';
		$this->setStatusFilter($request, $entryVendorTaskFilter);
		kReachUtils::setSelectedRelativeTime($request->getParam('from_time'), $entryVendorTaskFilter);
		return $entryVendorTaskFilter;

	}

	protected static function getPaginator($reachPluginClient, $partnerId, $entryVendorTaskFilter, $request, $page, $pageSize)
	{
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->entryVendorTask, 'listAction', $partnerId, $entryVendorTaskFilter);

		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		return $paginator;
	}

	protected static function getFilterForm($request, $action)
	{
		// set view
		$entryVendorTaskFilterForm = new Form_EntryVendorTasksFilter();
		$entryVendorTaskFilterForm->populate($request->getParams());
		$entryVendorTaskFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$entryVendorTaskFilterForm->setAction($entryVendorTaskFilterFormAction);
		return $entryVendorTaskFilterForm;
	}

	protected function getEntryVendorTaskFilter(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Reach_Type_EntryVendorTaskFilter();
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
		{
			return $filter;
		}
		$filterType = $request->getParam('filter_type');
		if ($filterType === 'partnerIdEqual')
		{
			Infra_ClientHelper::impersonate($filterInput);
		}
		else
		{
			$filter->$filterType = $filterInput;
		}
		return $filter;
	}

	protected function setStatusFilter($request, $entryVendorTaskFilter)
	{
		$filterStatusInput = $request->getParam('filter_status');
		if (strlen($filterStatusInput))
		{
			$entryVendorTaskFilter->statusEqual = $filterStatusInput;
		}
		else
		{
			$entryVendorTaskFilter->statusIn = EntryVendorTaskStatus::READY . ',' . EntryVendorTaskStatus::PENDING . ',' . EntryVendorTaskStatus::PROCESSING . ',' . EntryVendorTaskStatus::ERROR . ',' . EntryVendorTaskStatus::SCHEDULED;
		}
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
		}
		catch (Exception $e)
		{
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}

	private function addCreatedAtParamsToFilter($request, $action, &$entryVendorTaskFilter)
	{
		if ($request->getParam('createdAtFrom', false))
		{
			$createdAtFrom = new Zend_Date($this->_getParam('createdAtFrom'), 'MM/DD/YYYY');
			$entryVendorTaskFilter->createdAtGreaterThanOrEqual = $createdAtFrom->toString(Zend_Date::TIMESTAMP);
		}
		else
		{
			$createdAtFrom = $action->view->filterForm->getElement('createdAtFrom');
			$createdAtFrom->setValue(date('m/d/Y', $this->getDefaultFromDate()));
			$entryVendorTaskFilter->createdAtGreaterThanOrEqual = $this->getDefaultFromDate();
		}

		if ($request->getParam('createdAtTo', false))
		{
			$createdAtTo = new Zend_Date($this->_getParam('createdAtTo'), 'MM/DD/YYYY');
			$createdAtTo->addDay(1);
			$entryVendorTaskFilter->createdAtLessThanOrEqual = $createdAtTo->toString(Zend_Date::TIMESTAMP);
		}
		else
		{
			$createdAtTo = $action->view->filterForm->getElement('createdAtTo');
			$createdAtTo->setValue(date('m/d/Y', $this->getDefaultToDate()));
			$entryVendorTaskFilter->createdAtLessThanOrEqual = $this->getDefaultToDate();
		}
	}

	private function getDefaultFromDate()
	{
		return time() - (60 * 60 * 12);
	}

	private function getDefaultToDate()
	{
		return time() + (60 * 60 * 12);
	}
}

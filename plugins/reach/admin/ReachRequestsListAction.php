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
	}

	protected function initFilter($request)
	{
		$entryVendorTaskFilter = $this->getEntryVendorTaskFilter($request);
		$this->setCreatedAtFilter($entryVendorTaskFilter);
		$this->setStatusFilter($request, $entryVendorTaskFilter);
		$this->setSelectedRelativeTime($request, $entryVendorTaskFilter);
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

	protected function setCreatedAtFilter($entryVendorTaskFilter)
	{
		$createdAtStart = VendorServiceTurnAroundTime::TEN_DAYS + VendorServiceTurnAroundTime::TWENTY_FOUR_HOURS;
		$entryVendorTaskFilter->createdAtGreaterThanOrEqual = time() - $createdAtStart;
		$entryVendorTaskFilter->orderBy = '-createdAt';
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
			$entryVendorTaskFilter->statusIn = EntryVendorTaskStatus::PENDING .','. EntryVendorTaskStatus::PROCESSING.','.EntryVendorTaskStatus::ERROR;
		}
	}

	protected function setSelectedRelativeTime($request, $entryVendorTaskFilter)
	{
		$startTime = 0;
		$endTime = 0;
		$filterDateInput = $request->getParam('from_time');
		if (!preg_match('/^(\-|\+)(\d+$)/', $filterDateInput, $matches))
		{
			return;
		}
		$sign = $matches[1];
		$hours = (int)$matches[2];
		$timeFromHourToSec = $hours * 60 * 60;
		if ($sign === '-')
		{
			$startTime = time() - $timeFromHourToSec;
			$endTime = time();
		}
		else if ($sign === '+')
		{
			$startTime = time();
			$endTime = time() + $timeFromHourToSec;
		}
		if ($startTime && $endTime)
		{
			$entryVendorTaskFilter->expectedFinishTimeGreaterThanOrEqual = $startTime;
			$entryVendorTaskFilter->expectedFinishTimeLessThanOrEqual = $endTime;
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
}
<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachRequestsListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'ReachRequestsListAction';
		$this->label = "Reach Requests";
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
		$partnerId = null;

		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// init filter
		$entryVendorTaskFilter = $this->getEntryVendorTaskFilter($request);
		$entryVendorTaskFilter->orderBy = "-createdAt";
		$this->setStatusFilter($request, $entryVendorTaskFilter);
		//$this->setSelectedRelativeTime($request, $entryVendorTaskFilter);


		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->entryVendorTask, "listAction", $partnerId, $entryVendorTaskFilter);

		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$entryVendorTaskFilterForm = new Form_EntryVendorTasksFilter();
		$entryVendorTaskFilterForm->populate($request->getParams());
		$entryVendorTaskFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$entryVendorTaskFilterForm->setAction($entryVendorTaskFilterFormAction);

		$action->view->filterForm = $entryVendorTaskFilterForm;
		$action->view->paginator = $paginator;
	//	Infra_ClientHelper::unimpersonate();
		//$createProfileForm = new Form_CreateEntryVendorTask();

		//$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'ReachRequestsConfigure'), null, true);
		//$createProfileForm->setAction($actionUrl);

	//	if ($partnerId)
	//		$createProfileForm->getElement("newPartnerId")->setValue($partnerId);

		//$action->view->newEntryVendorTaskFolderForm = $createProfileForm;
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
		if ($filterType === "partnerIdEqual")
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
			$entryVendorTaskFilter->statusIn = EntryVendorTaskStatus::PENDING .','. EntryVendorTaskStatus::PROCESSING.','.EntryVendorTaskStatus::ERROR;
		}
	}

	protected function setSelectedRelativeTime($request, $entryVendorTaskFilter)
	{
		$startTime = 0;
		$endTime = 0;
		$filterDateInput = $request->getParam('from_time');
		if (strlen($filterDateInput) > 2)
		{
			if ($filterDateInput[0] == '-')
			{
				$startTime = time() - substr($filterDateInput,1);
				$endTime = time();
			}
			else if ($filterDateInput[0] == '+')
			{
				$startTime = time();
				$endTime = time() + substr($filterDateInput,1);
			}
			if ($startTime && $endTime)
			{
				$entryVendorTaskFilter->expectedFinishTimeGreaterThanOrEqual = $startTime;
				$entryVendorTaskFilter->expectedFinishTimeLessThanOrEqual = $endTime;
			}
		}
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
		{
			return $this;
		}
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


	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array(0 => 'Reach', 1 => 'listReachRequests');
		return $options;
	}

	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listReachRequests(partnerId)
		    {
					var url = pluginControllerUrl + \'/' . get_class($this) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
	                document.location = url;
	        }';

		return $functionStr;
	}
}
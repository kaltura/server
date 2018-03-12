<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachProfileListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'ReachProfileListAction';
		$this->label = "Reach Profiles";
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
		$reachProfileFilter = new Kaltura_Client_Reach_Type_ReachProfileFilter();
		$reachProfileFilter->orderBy = "-createdAt";

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->reachProfile, "listAction", $partnerId, $reachProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$reachProfileFilterForm = new Form_ReachProfileFilter();
		$reachProfileFilterForm->populate($request->getParams());
		$reachProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$reachProfileFilterForm->setAction($reachProfileFilterFormAction);

		$action->view->filterForm = $reachProfileFilterForm;
		$action->view->paginator = $paginator;

		$createReachProfileForm = new Form_CreateReachProfile();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'ReachProfileConfigure'), null, true);
		$createReachProfileForm->setAction($actionUrl);

		if ($partnerId)
			$createReachProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newReachProfileFolderForm = $createReachProfileForm;

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
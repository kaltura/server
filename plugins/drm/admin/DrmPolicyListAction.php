<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmPolicyListAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'DrmPolicyListAction';
		$this->label = 'Drm Policies';
		$this->rootLabel = 'DRM';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DRM_POLICY_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);

		// Init filter
		$filter = $this->getFilterFromRequest($request, new Kaltura_Client_Drm_Type_DrmPolicyFilter());
		$filter->orderBy = "-createdAt";

		// Get client
		$client = Infra_ClientHelper::getClient();
		$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
		
		// Get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($drmPluginClient->drmPolicy, "listAction", null, $filter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// Set filter view
		$filterForm = new Form_DrmPolicyFilter();
		$filterForm->populate($request->getParams());
		$filterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$filterForm->setAction($filterFormAction);
		
		$action->view->filterForm = $filterForm;
		$action->view->paginator = $paginator;

		// Set create view
		$createForm = new Form_CreateDrmPolicy();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'DrmPolicyConfigure'), null, true);
		$createForm->setAction($actionUrl);
		
		if($filter && isset($filter->partnerIdEqual))
			$createForm->getElement("newPartnerId")->setValue($filter->partnerIdEqual);
			
		$action->view->createForm = $createForm;
	}
}


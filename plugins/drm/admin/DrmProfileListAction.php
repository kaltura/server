<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmProfileListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'DrmProfileListAction';
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
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DRM_PROFILE_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('partnerId');
		
		// init filter
		$drmProfileFilter = $this->getDrmProfileFilterFromRequest($request);
		$drmProfileFilter->orderBy = "-createdAt";
		
		$client = Infra_ClientHelper::getClient();
		$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($drmPluginClient->drmProfile, "listAction", null, $drmProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$drmProfileFilterForm = new Form_DrmProfileFilter();
		$drmProfileFilterForm->populate ( $request->getParams () );
		$drmProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$drmProfileFilterForm->setAction($drmProfileFilterFormAction);
		
		$action->view->filterForm = $drmProfileFilterForm;
		$action->view->paginator = $paginator;

		$createProfileForm = new Form_CreateDrmProfile();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'DrmProfileConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);
		
		if($drmProfileFilter && isset($drmProfileFilter->partnerIdEqual))
			$createProfileForm->getElement("newPartnerId")->setValue($drmProfileFilter->partnerIdEqual);
			
		$action->view->newProfileForm = $createProfileForm;
	}
	
	
	private function getDrmProfileFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Drm_Type_DrmProfileFilter();
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
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
		$options[] = array (0 => 'Drm Profiles', 1 => 'listDrmProfiles');
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listDrmProfiles(partnerId) {
			var url = pluginControllerUrl + \'/'.get_class($this).'/filter_type/partnerIdEqual/filter_input/\' + partnerId;
			document.location = url;
		}';
		return $functionStr;
	}
	
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}

	
}


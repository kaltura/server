<?php
class DropFolderListAction extends KalturaAdminConsolePlugin
{
	public function __construct()
	{
		$this->action = 'listDropFolders';
		$this->label = 'Drop Folders';
		$this->rootLabel = 'Publishers';
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DROP_FOLDER_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('partnerId');
		
		$dropFolderFilterForm = new Form_DropFolderFilter();

		// init filter
		$dropFolderFilter = $this->getDropFolderFilterFromRequest($request);
		
		$client = Infra_ClientHelper::getClient();
		$dropFolderPluginClient = Kaltura_Client_DropFolder_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($dropFolderPluginClient->dropFolder, "listAction", null, $dropFolderFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$action->view->paginator = $paginator;
		$action->view->filterForm = $dropFolderFilterForm;
		
		$createFolderForm = new Form_CreateDropFolder();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'DropFolderConfigure'), null, true);
		$createFolderForm->setAction($actionUrl);
		$action->view->newFolderForm = $createFolderForm;
	}
	
	
	private function getDropFolderFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
			return null;
			
		$filter = new Kaltura_Client_DropFolder_Type_DropFolderFilter();
		$filterType = $request->getParam('filter_type');
		$filter->$filterType = $filterInput;
		
		return $filter;
	}
	
}


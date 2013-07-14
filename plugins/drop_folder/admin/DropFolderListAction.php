<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class DropFolderListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'DropFolderListAction';
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DROP_FOLDER_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$partnerId = $this->_getParam('partnerId');
		
		// init filter
		$dropFolderFilter = $this->getDropFolderFilterFromRequest($request);
		$dropFolderFilter->orderBy = "-createdAt";
		
		$client = Infra_ClientHelper::getClient();
		$dropFolderPluginClient = Kaltura_Client_DropFolder_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($dropFolderPluginClient->dropFolder, "listAction", null, $dropFolderFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$dropFolderFilterForm = new Form_DropFolderFilter();
		$dropFolderFilterForm->populate ( $request->getParams () );
		$dropFolderFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$dropFolderFilterForm->setAction($dropFolderFilterFormAction);
		
		$action->view->filterForm = $dropFolderFilterForm;
		$action->view->paginator = $paginator;

		$createFolderForm = new Form_CreateDropFolder();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'DropFolderConfigure'), null, true);
		$createFolderForm->setAction($actionUrl);
		
		if($dropFolderFilter && isset($dropFolderFilter->partnerIdEqual))
			$createFolderForm->getElement("newPartnerId")->setValue($dropFolderFilter->partnerIdEqual);
			
		$action->view->newFolderForm = $createFolderForm;
	}
	
	
	private function getDropFolderFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_DropFolder_Type_DropFolderFilter();
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
		$options[] = array (0 => 'Drop Folders', 1 => 'listDropFolders');
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listDropFolders(partnerId) {
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


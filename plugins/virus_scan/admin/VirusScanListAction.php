<?php
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class VirusScanListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	
	public function __construct()
	{
		$this->action = 'VirusScanListAction';
		$this->label = null;
		$this->rootLabel = null;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath ( dirname ( __FILE__ ) );
	}
	
	public function getRequiredPermissions()
	{
		return array (Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_VIRUS_SCAN );
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$client = Infra_ClientHelper::getClient ();
		$virusScanPlugin = Kaltura_Client_VirusScan_Plugin::get ( $client );
		
		//create new form
		$newForm = new Form_NewVirusScanProfile ();
		
		$page = $this->_getParam ( 'page', 1 );
		$pageSize = $this->_getParam ( 'pageSize', 10 );
		
		//get PartnerId
		$partnerId = $this->_getParam ( 'partnerId' );
		//filter form
		$request = $action->getRequest ();
		$virusScanFilterForm = new Form_VirusScanFilter ();
		$virusScanFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
  		$virusScanFilterForm->setAction($virusScanFilterFormAction);
		$virusScanFilterForm->populate ( $request->getParams () );
		$virusScanFilter = $this->getVirusScanFilterFromRequest ( $request );
		
		$newForm->getElement("newPartnerId")->setValue($virusScanFilter->partnerIdEqual);
		//filter also by partnerId
		if (! is_null ( $partnerId )) {
			$virusScanFilter->partnerIdEqual = $partnerId;
		}
		
		$paginatorAdapter = new Infra_FilterPaginator ( $virusScanPlugin->virusScanProfile, "listAction", null, $virusScanFilter );
		$paginator = new Infra_Paginator ( $paginatorAdapter );
		$paginator->setCurrentPageNumber ( $page );
		$paginator->setItemCountPerPage ( $pageSize );
		
		$action->view->virusScanFilterForm = $virusScanFilterForm;
		$action->view->newForm = $newForm;
		$action->view->paginator = $paginator;
	}
	
	private function getVirusScanFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_VirusScan_Type_VirusScanProfileFilter ();
		$filterInput = $request->getParam ( 'filter_input' );
		if (strlen ( $filterInput )) {
			$filterType = $request->getParam ( 'filter_type' );
			$filter->$filterType = $filterInput;
		}
		
		return $filter;
	}
	
	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName> 
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array ();
		$options [] = array (0 => 'Virus Scan', 1 => 'listVirusScanProfiles' );
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listVirusScanProfiles(partnerId) {
			var url = pluginControllerUrl + \'/' . get_class ( $this ) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
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
}


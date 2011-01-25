<?php
class GenericDistributionProvidersListAction extends KalturaAdminConsolePlugin
{
	public function __construct()
	{
		$this->action = 'listGenericDistributionProviders';
		$this->label = 'Generic Providers';
		$this->rootLabel = 'Distribution';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRole()
	{
		return Kaltura_AclHelper::ROLE_ADMINISTRATOR;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$newForm = new Form_NewGenericProvider();
		
		// init filter
		$genericProviderFilter = new KalturaGenericDistributionProviderFilter();
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("genericDistributionProvider", "listAction", null, $genericProviderFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$action->view->newForm = $newForm;
		$action->view->paginator = $paginator;
	}
}


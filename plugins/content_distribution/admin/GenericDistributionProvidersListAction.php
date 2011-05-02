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
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);

		$newForm = new Form_NewGenericProvider();
		
		// init filter
		$genericProviderFilter = new Kaltura_Client_ContentDistribution_Type_GenericDistributionProviderFilter();
		
		$client = Infra_ClientHelper::getClient();
		$contentDistributionPlugin = Kaltura_Client_ContentDistribution_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($contentDistributionPlugin->genericDistributionProvider, "listAction", null, $genericProviderFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// set view
		$action->view->newForm = $newForm;
		$action->view->paginator = $paginator;
	}
}


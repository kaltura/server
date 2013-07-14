<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class GenericDistributionProvidersListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'listGenericDistributionProviders';
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

	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName> 
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array (0 => 'Generic Providers', 1 => 'distributionProviders');
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function distributionProviders(partnerId) {
			var url = pluginControllerUrl + /'.get_class($this).'/ + \'filter_type/byid/filter_input/\' + partnerId;
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



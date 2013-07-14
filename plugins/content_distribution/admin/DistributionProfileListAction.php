<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class DistributionProfileListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'listDistributionProfiles';
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
	
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Type_PartnerFilter();
		
		$filterInput = $request->getParam('filter_input');
		if(!strlen($filterInput))
			return $filter;
			
		$filterType = $request->getParam('filter_type');
		if ($filterType == 'byid')
		{
			$filter->idIn = $filterInput;
		}
		else
		{
			if ($filterType == 'byname')
				$filter->nameLike = $filterInput;
			elseif ($filterType == 'free' && $filterInput)
				$filter->partnerNameDescriptionWebsiteAdminNameAdminEmailLike = $filterInput;
		}
		return $filter;
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$form = new Form_PartnerIdFilter();
		$form->populate($request->getParams());
		
		$newForm = new Form_NewDistributionProfile();
		
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'DistributionProfileListAction'), null, true);
		$form->setAction($actionUrl);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		$client = Infra_ClientHelper::getClient();
		$contentDistributionPlugin = Kaltura_Client_ContentDistribution_Plugin::get($client);
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($contentDistributionPlugin->distributionProfile, "listByPartner", null, $partnerFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		$providers = array(
			Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::GENERIC => 'Generic',
			Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::SYNDICATION => 'Syndication'
		);
		$genericProviders = array();
		$client = Infra_ClientHelper::getClient();
		$contentDistributionClientPlugin = Kaltura_Client_ContentDistribution_Plugin::get($client);
		$providersList = $contentDistributionClientPlugin->distributionProvider->listAction();
		if($providersList)
		{
			foreach($providersList->objects as $provider)
			{
				if($provider->type == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::GENERIC)
					$genericProviders[$provider->id] = $provider->name;
				else
					$providers[$provider->type] = $provider->name;
			}
		}
		$newForm->setProviders($providers);
		$newPartnerElement = $newForm->getElement('newPartnerId');
		if($newPartnerElement)
			$newPartnerElement->setValue($partnerFilter->idIn);
		
		// set view
		$action->view->form = $form;
		$action->view->newForm = $newForm;
		$action->view->paginator = $paginator;
		$action->view->providers = $providers;
		$action->view->genericProviders = $genericProviders;
		
	}
	
	
	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName> 
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array (0 => 'Distribution Profiles', 1 => 'distributionProfiles');
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function distributionProfiles(partnerId) {
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


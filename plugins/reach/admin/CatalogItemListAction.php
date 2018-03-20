<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'CatalogItemListAction';
		$this->label = "Catalog Items";
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
		$vendorPartnerId = $this->_getParam('filter_input') ? $this->_getParam('filter_input') : $request->getParam('partnerId');
		$serviceFeature = $this->_getParam('filterServiceFeature') != "" ? $this->_getParam('filterServiceFeature') : null;
		$serviceType = $this->_getParam('filterServiceType') != "" ? $this->_getParam('filterServiceType') : null;
		$turnAroundTime = $this->_getParam('filterTurnAroundTime') != "" ? $this->_getParam('filterTurnAroundTime') : null;
		$sourceLanguage = $this->_getParam('filterSourceLanguage') != "" ? $this->_getParam('filterSourceLanguage') : null;
		$targetLanguage = $this->_getParam('filterTargetLanguage') != "" ? $this->_getParam('filterTargetLanguage') : null;
		$partnerId = null;

		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		// init filter
		$catalogItemProfileFilter = $this->getCatalogItemFilter($serviceFeature);
		$catalogItemProfileFilter->orderBy = "-createdAt";
		$catalogItemProfileFilter->serviceFeatureEqual = $serviceFeature;
		$catalogItemProfileFilter->serviceTypeEqual = $serviceType;
		$catalogItemProfileFilter->turnAroundTimeEqual = $turnAroundTime;
		$catalogItemProfileFilter->vendorPartnerIdEqual = $vendorPartnerId;
		$catalogItemProfileFilter->sourceLanguageEqual = $sourceLanguage;

		if($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
			$catalogItemProfileFilter->targetLanguageEqual = $targetLanguage;

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", $partnerId, $catalogItemProfileFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$catalogItemProfileFilterForm = new Form_CatalogItemFilter();
		$catalogItemProfileFilterForm->populate($request->getParams());
		$catalogItemProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$catalogItemProfileFilterForm->setAction($catalogItemProfileFilterFormAction);

		$action->view->filterForm = $catalogItemProfileFilterForm;
		$action->view->paginator = $paginator;

		$createProfileForm = new Form_CreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'CatalogItemConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);

		if ($partnerId)
			$createProfileForm->getElement("newPartnerId")->setValue($partnerId);

		$action->view->newCatalogItemFolderForm = $createProfileForm;
	}
	
	protected function getCatalogItemFilter($serviceFeature)
	{
		if ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
			return new Kaltura_Client_Reach_Type_VendorCaptionsCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
			return new Kaltura_Client_Reach_Type_VendorTranslationCatalogItemFilter();
		else
			return new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
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
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}
}

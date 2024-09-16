<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
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

		$catalogItemFilter = $this->getCatalogItemFilter($action);

		$action->view->allowed = $this->isAllowedForPartner(null);

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", null, $catalogItemFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
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

		$action->view->newCatalogItemFolderForm = $createProfileForm;
	}
	
	protected function getCatalogItemFilterByServiceFeature($serviceFeature)
	{
		if ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
			return new Kaltura_Client_Reach_Type_VendorCaptionsCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
			return new Kaltura_Client_Reach_Type_VendorTranslationCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::ALIGNMENT)
			return new Kaltura_Client_Reach_Type_VendorAlignmentCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::AUDIO_DESCRIPTION)
			return new Kaltura_Client_Reach_Type_VendorAudioDescriptionCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION)
			return new Kaltura_Client_Reach_Type_VendorExtendedAudioDescriptionCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::CHAPTERING)
			return new Kaltura_Client_Reach_Type_VendorChapteringCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING)
			return new Kaltura_Client_Reach_Type_VendorDubbingCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_CAPTION)
			return new Kaltura_Client_Reach_Type_VendorLiveCaptionCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::CLIPS)
			return new Kaltura_Client_Reach_Type_VendorClipsCatalogItemFilter();
		else
			return new Kaltura_Client_Reach_Type_VendorCatalogItemFilter();
	}

	protected function getCatalogItemFilter($action)
	{
		$request = $action->getRequest();

		$serviceFeature = $this->_getParam('filterServiceFeature') != "" ? $this->_getParam('filterServiceFeature') : null;
		$serviceType = $this->_getParam('filterServiceType') != "" ? $this->_getParam('filterServiceType') : null;
		$turnAroundTime = $this->_getParam('filterTurnAroundTime') != "" ? $this->_getParam('filterTurnAroundTime') : null;
		$sourceLanguage = $this->_getParam('filterSourceLanguage') != "" ? $this->_getParam('filterSourceLanguage') : null;
		$targetLanguage = $this->_getParam('filterTargetLanguage') != "" ? $this->_getParam('filterTargetLanguage') : null;

		$catalogItemFilter = $this->getCatalogItemFilterByServiceFeature($serviceFeature);
		$catalogItemFilter->orderBy = "-id";
		$catalogItemFilter->serviceFeatureEqual = $serviceFeature;
		$catalogItemFilter->serviceTypeEqual = $serviceType;
		$catalogItemFilter->turnAroundTimeEqual = $turnAroundTime;
		$catalogItemFilter->sourceLanguageEqual = $sourceLanguage;

		$filterInput = $request->getParam('filter_input');
		if (strlen($filterInput))
		{
			$filterType = $request->getParam('filter_type');
			$catalogItemFilter->$filterType = $filterInput;
		}

		if(in_array($serviceFeature, array(Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION, Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING)))
		{
			$catalogItemFilter->targetLanguageEqual = $targetLanguage;
		}

		return $catalogItemFilter;
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

	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array(0 => 'Reach', 1 => 'listCatalogItems');
		return $options;
	}
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listCatalogItems(partnerId)
		    {
					var url = pluginControllerUrl + \'/' . get_class($this) . '/filter_type/partnerIdEqual/filter_input/\' + partnerId;
	                document.location = url;
	        }';
		
		return $functionStr;
	}
}

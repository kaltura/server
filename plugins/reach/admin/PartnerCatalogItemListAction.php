<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnerCatalogItemListAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";

	public function __construct()
	{
		$this->action = 'PartnerCatalogItemListAction';
		$this->label = "Partner Catalog Items";
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

		$partnerId = null;
		$catalogItemId = null;
		if ($this->_getParam('filter_input'))
		{
			$filterType = $this->_getParam('filter_type');
			if ($filterType === 'partnerIdEqual')
			{
				$partnerId = $this->_getParam('filter_input');
			}
			elseif ($filterType === 'catalogItemIdEqual')
			{
				$catalogItemId = $this->_getParam('filter_input');
			}
		}

		$serviceFeature = $this->_getParam('filterServiceFeature') != "" ? $this->_getParam('filterServiceFeature') : null;
		$serviceType = $this->_getParam('filterServiceType') != "" ? $this->_getParam('filterServiceType') : null;
		$turnAroundTime = $this->_getParam('filterTurnAroundTime') != "" ? $this->_getParam('filterTurnAroundTime') : null;
		$sourceLanguage = $this->_getParam('filterSourceLanguage') != "" ? $this->_getParam('filterSourceLanguage') : null;
		$targetLanguage = $this->_getParam('filterTargetLanguage') != "" ? $this->_getParam('filterTargetLanguage') : null;
		$vendorPartnerId = $this->_getParam('vendorPartnerId') != "" ? $this->_getParam('vendorPartnerId') : null;

		$action->view->allowed = $this->isAllowedForPartner($partnerId);
		if ($partnerId || $catalogItemId)
		{
			$vendorCatalogItemFilter = $this->getCatalogItemFilter($serviceFeature);
			$vendorCatalogItemFilter->orderBy = "-createdAt";
			$vendorCatalogItemFilter->serviceTypeEqual = $serviceType;
			$vendorCatalogItemFilter->turnAroundTimeEqual = $turnAroundTime;
			$vendorCatalogItemFilter->partnerIdEqual = $partnerId;
			$vendorCatalogItemFilter->sourceLanguageEqual = $sourceLanguage;
			$vendorCatalogItemFilter->vendorPartnerIdEqual = $vendorPartnerId;
			$vendorCatalogItemFilter->catalogItemIdEqual = $catalogItemId;

			if(in_array($serviceFeature, array(Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION, Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING)))
			{
				$vendorCatalogItemFilter->targetLanguageEqual = $targetLanguage;
			}

			$client = Infra_ClientHelper::getClient();
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

			// get results and paginate
			Infra_ClientHelper::unimpersonate();
			$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", $partnerId, $vendorCatalogItemFilter);

			// init filter
			$paginator = new Infra_Paginator($paginatorAdapter, $request);
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage($pageSize);
			$action->view->paginator = $paginator;
		}

		// set view
		$catalogItemProfileFilterForm = new Form_PartnerCatalogItemFilter();
		$catalogItemProfileFilterForm->populate($request->getParams());
		$catalogItemProfileFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$catalogItemProfileFilterForm->setAction($catalogItemProfileFilterFormAction);

		$action->view->filterForm = $catalogItemProfileFilterForm;

		$createProfileForm = new Form_PartnerCreateCatalogItem();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'PartnerCatalogItemConfigure'), null, true);
		$createProfileForm->setAction($actionUrl);

		$action->view->newPartnerCatalogItemFolderForm = $createProfileForm;

		$clonePartnerCatalogItemsForm = new Form_ClonePartnerCatalogItems();
		$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'PartnerCatalogItemClone'), null, true);
		$clonePartnerCatalogItemsForm->setAction($actionUrl);

		$action->view->clonePartnerCatalogItemsForm = $clonePartnerCatalogItemsForm;
		$action->view->partnerId = $partnerId;
	}

	protected function getCatalogItemFilter($serviceFeature)
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
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_TRANSLATION)
			return new Kaltura_Client_Reach_Type_VendorLiveTranslationCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::CLIPS)
			return new Kaltura_Client_Reach_Type_VendorClipsCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::QUIZ)
			return new Kaltura_Client_Reach_Type_VendorQuizCatalogItemFilter();
		elseif ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::OCR)
			return new Kaltura_Client_Reach_Type_VendorOcrCatalogItemFilter();
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
		}
		catch (Exception $e)
		{
			$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);
			return false;
		}
		$client->setPartnerId(self::ADMIN_CONSOLE_PARTNER);

		$isAllowed = ($result->totalCount > 0) && ($result->objects[0]->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE);
		return $isAllowed;
	}
}

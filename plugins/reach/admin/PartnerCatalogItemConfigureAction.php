<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class PartnerCatalogItemConfigureAction extends KalturaApplicationPlugin
{
	const ADMIN_CONSOLE_PARTNER = "-2";
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$partnerId = $this->_getParam('partner_id');
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = $this->initForm($action);

		try
		{
			if ($action->getRequest()->isPost())
				$form = $this->handlePost($action,$partnerId);
			else
				$form = $this->handleConfigureCatalogItem($action, $partnerId);
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		Infra_ClientHelper::unimpersonate();
		$action->view->form = $form;
	}

	/***
	 * @param $action
	 * @return Form_CatalogItemConfigure|null
	 * @throws Zend_Form_Exception
	 */
	protected function handleConfigureCatalogItem($action, $partnerId)
	{
		$form = $this->initForm($action);
		$request = $action->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$serviceFeature = $this->_getParam('serviceFeature') != "" ? $this->_getParam('serviceFeature') : null;
		$ServiceType = $this->_getParam('serviceType') != "" ? $this->_getParam('serviceType') : null;
		$turnAround = $this->_getParam('turnAroundTime') != "" ? $this->_getParam('turnAroundTime') : null;
		$sourceLanguage = $this->_getParam('sourceLanguage') != "" ? $this->_getParam('sourceLanguage') : null;
		$targetLanguage = $this->_getParam('targetLanguage') != "" ? $this->_getParam('targetLanguage') : null;
		$vendorPartnerId = $this->_getParam('vendorPartnerId') != "" ? $this->_getParam('vendorPartnerId') : null;


		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		if ($action->view->allowed)
		{
			$partnerCatalogItems = $this->getPartnerCatalogItems($partnerId);

			Infra_ClientHelper::unimpersonate();// to get all catalog items from partner 0
			// init filter
			$catalogItemProfileFilter = $this->getCatalogItemFilter($serviceFeature);
			$catalogItemProfileFilter->orderBy = "-createdAt";
			$catalogItemProfileFilter->serviceTypeEqual = $ServiceType;
			$catalogItemProfileFilter->turnAroundTimeEqual = $turnAround;
			$catalogItemProfileFilter->idNotIn = implode(',', $partnerCatalogItems);
			$catalogItemProfileFilter->sourceLanguageEqual = $sourceLanguage;
			$catalogItemProfileFilter->vendorPartnerIdEqual = $vendorPartnerId;
			$catalogItemProfileFilter->statusEqual =  Kaltura_Client_Reach_Enum_VendorCatalogItemStatus::ACTIVE;

			if($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
				$catalogItemProfileFilter->targetLanguageEqual = $targetLanguage;

			$client = Infra_ClientHelper::getClient();
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);

			// get results and paginate
			$paginatorAdapter = new Infra_FilterPaginator($reachPluginClient->vendorCatalogItem, "listAction", null, $catalogItemProfileFilter);
			$paginator = new Infra_Paginator($paginatorAdapter, $request);
			$paginator->setSubmitFunction("loadPartnerCatalogItems");
			$paginator->setIndex(2);
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage($pageSize);

			$form->populate($request->getParams());
			$action->view->paginator = $paginator;
			$action->view->vendorPartnerId = $vendorPartnerId;
		}
		return $form;
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
	/***
	 * @param $action
	 * @param ConfigureForm $form
	 * @param null $catalogItemId
	 * @param null $cloneTemplateId
	 * @throws Zend_Form_Exception
	 */
	protected function handlePost($action, $partnerId)
	{
		$form = $this->initForm($action);
		$action->view->allowed = $this->isAllowedForPartner($partnerId);

		if ($action->view->allowed)
		{
			$formData = $action->getRequest()->getPost();
			if ($formData['selectAllItemsCheckbox'])
			{
				$partnerCatalogItems = $this->getAvailableCatalogItems($partnerId);

			}
			else
			{
				$partnerCatalogItems = $formData['catalogItemsCheckBoxes'];
			}
			$this->client = Infra_ClientHelper::getClient();
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
			Infra_ClientHelper::impersonate($partnerId);
			$this->client->startMultiRequest();
			foreach ($partnerCatalogItems as $partnerCatalogItem)
				$partnerCatalogItem = $reachPluginClient->PartnerCatalogItem->add($partnerCatalogItem);
			$result = $this->client->doMultiRequest();
		}

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
		return $form;
	}

	protected function getAvailableCatalogItems($partnerId)
	{
		$partnerCatalogItems = $this->getPartnerCatalogItems($partnerId);
		return $this->getPartnerCatalogItems( null , $partnerCatalogItems);
	}

	protected function getPartnerCatalogItems($partnerId = null, $partnerCatalogItemsNoIn = null)
	{
		$serviceFeature = $this->_getParam('serviceFeature') != "" ? $this->_getParam('serviceFeature') : null;
		$ServiceType = $this->_getParam('serviceType') != "" ? $this->_getParam('serviceType') : null;
		$turnAround = $this->_getParam('turnAroundTime') != "" ? $this->_getParam('turnAroundTime') : null;
		$sourceLanguage = $this->_getParam('sourceLanguage') != "" ? $this->_getParam('sourceLanguage') : null;
		$targetLanguage = $this->_getParam('targetLanguage') != "" ? $this->_getParam('targetLanguage') : null;
		$vendorPartnerId = $this->_getParam('vendorPartnerId') != "" ? $this->_getParam('vendorPartnerId') : null;

		$catalogItemProfileFilter = $this->getCatalogItemFilter($serviceFeature);
		$catalogItemProfileFilter->orderBy = "-createdAt";
		$catalogItemProfileFilter->serviceTypeEqual = $ServiceType;
		$catalogItemProfileFilter->turnAroundTimeEqual = $turnAround;

		if ($partnerId)
			$catalogItemProfileFilter->partnerIdEqual = $partnerId;
		else
			Infra_ClientHelper::unimpersonate();

		$catalogItemProfileFilter->sourceLanguageEqual = $sourceLanguage;
		$catalogItemProfileFilter->vendorPartnerIdEqual = $vendorPartnerId;

		if ($partnerCatalogItemsNoIn)
			$catalogItemProfileFilter->idNotIn = implode(',', $partnerCatalogItemsNoIn);

		if ($serviceFeature == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
			$catalogItemProfileFilter->targetLanguageEqual = $targetLanguage;

		$this->client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		Infra_ClientHelper::impersonate($partnerId);

		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = 500;

		$partnerCatalogItems = array();
		$result = $reachPluginClient->vendorCatalogItem->listAction($catalogItemProfileFilter, $pager);
		$totalCount = $result->totalCount;
		while ($totalCount > 0)
		{
			foreach ($result->objects as $partnerCatalogItem)
			{
				/* @var $partnerCatalogItem Kaltura_Client_Reach_Type_VendorCatalogItem */
				$partnerCatalogItems[] = $partnerCatalogItem->id;
			}
			$pager->pageIndex++;
			$totalCount = $totalCount - $pager - pageSize;
			$result = $reachPluginClient->vendorCatalogItem->listAction($catalogItemProfileFilter, $pager);
		}
		return $partnerCatalogItems;
	}

	protected function initForm(Zend_Controller_Action $action)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'PartnerCatalogItemConfigureAction',
		);

		$form = new Form_PartnerCatalogItemConfigure();
		$form->setAction($action->view->url($urlParams));
		return $form;
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
<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemConfigureAction extends KalturaApplicationPlugin
{
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
		$this->client = Infra_ClientHelper::getClient();
		$partnerId = $this->_getParam('new_partner_id');
		$catalogItemId = $this->_getParam('catalog_item_id');
		$cloneTemplateId = $this->_getParam('clone_template_id');
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			if ($cloneTemplateId)
				$form  = $this->handleClone($action, $cloneTemplateId, $partnerId);
			elseif ($catalogItemId)
				$form = $this->handleExistingCatalogItem($action, $catalogItemId, $partnerId);
			else
				$form = $this->handleNewCatalogItem($action, $partnerId);
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			if ($form)
			{
				$formData = $action->getRequest()->getPost();
				$form->populate($formData);
				if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
				elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);
			}
		}
		Infra_ClientHelper::unimpersonate();
		$action->view->form = $form;
		$action->view->catalogItemId = $catalogItemId;
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param $cloneTemplateId
	 * @param $partnerId
	 * @return array
	 */
	protected function handleClone(Zend_Controller_Action $action, $cloneTemplateId, $partnerId)
	{
		$request = $action->getRequest();
		if ($request->isPost())
		{
			$form = $this->initForm($action, $partnerId, null, null, null, $cloneTemplateId);
			$this->handlePost($action, $form, null, $cloneTemplateId);
		} else
		{
			Infra_ClientHelper::unimpersonate(); // we need to retrieve template catalog item from partner 0
			$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
			$catalogItem = $reachPluginClient->vendorCatalogItem->get($cloneTemplateId);
			$catalogItemType = $catalogItem->serviceFeature;
			$catalogItemServiceType = $catalogItem->serviceType;
			$turnAroundTime = $catalogItem->turnAroundTime;
			$form = $this->initForm($action, $partnerId, $catalogItemType,$catalogItemServiceType, $turnAroundTime, $cloneTemplateId);
			$form->populateFromObject($catalogItem, false);
		}
		return $form;
	}

	/***
	 * @param $action
	 * @param $catalogItemId
	 * @param $partnerId
	 * @return Form_CatalogItemConfigure
	 */
	protected function handleExistingCatalogItem($action, $catalogItemId, $partnerId)
	{
		$request = $action->getRequest();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$catalogItem = $reachPluginClient->vendorCatalogItem->get($catalogItemId);
		$catalogItemType = $catalogItem->serviceFeature;
		$serviceType = $catalogItem->serviceType;
		$turnAroundTime = $catalogItem->turnAroundTime;
		$form = $this->initForm($action, $partnerId, $catalogItemType, $serviceType, $turnAroundTime, $catalogItem->id);
		if ($request->isPost())
			$this->handlePost($action, $form, $catalogItem->id);
		else
			$form->populateFromObject($catalogItem, false);
		return $form;
	}

	/***
	 * @param $action
	 * @return Form_CatalogItemConfigure|null
	 * @throws Zend_Form_Exception
	 */
	protected function handleNewCatalogItem($action, $partnerId)
	{
		$request = $action->getRequest();
		$catalogItemType = $this->_getParam('new_catalog_item_type');
		$catalogItemServiceType = $this->_getParam('new_catalog_item_service_type');
		$catalogItemTurnAroundTime = $this->_getParam('new_catalog_item_turn_around_time');
		$form = $this->initForm($action, $partnerId, $catalogItemType, $catalogItemServiceType, $catalogItemTurnAroundTime);

		if ($request->isPost())
			$this->handlePost($action, $form);
		else
		{
			$form->getElement('serviceFeature')->setValue($catalogItemType);
		}
		return $form;
	}

	/***
	 * @param $action
	 * @param ConfigureForm $form
	 * @param null $catalogItemId
	 * @param null $cloneTemplateId
	 * @throws Zend_Form_Exception
	 */
	protected function handlePost($action, ConfigureForm $form, $catalogItemId = null, $cloneTemplateId = null)
	{
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$formData = $action->getRequest()->getPost();
		$form->populate($formData);
		if ($form->isValid($formData))
		{
			if ($cloneTemplateId)
			{
				$catalogItem = $reachPluginClient->vendorCatalogItem->cloneAction($cloneTemplateId);
				$catalogItemId = $catalogItem->id;
			}

			if ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
				$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
			elseif ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
				$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

			$form->resetUnUpdatebleAttributes($catalogItem);
			if($catalogItemId)
				$catalogItem = $reachPluginClient->vendorCatalogItem->update($catalogItemId, $catalogItem);
			else
				$catalogItem = $reachPluginClient->vendorCatalogItem->add($catalogItem);
			$form->setAttrib('class', 'valid');
			$action->view->formValid = true;
		}
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @param null $catalogItemType
	 * @param null $catalogItemId
	 * @param null $catalogItemServiceType
	 * @param null $catalogItemTurnAroundTime
	 * @return Form_CatalogItemConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $partnerId, $catalogItemType = null, $catalogItemServiceType = null, $catalogItemTurnAroundTime = null, $catalogItemId = null)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'CatalogItemConfigureAction',
		);
		if ($catalogItemId)
		{
			$form = new Form_CatalogItemConfigure($partnerId, $catalogItemType, $catalogItemServiceType, $catalogItemTurnAroundTime, true);
			$urlParams['catalog_item_id'] = $catalogItemId;
		}
		else
			$form = new Form_CatalogItemConfigure($partnerId, $catalogItemType, $catalogItemServiceType, $catalogItemTurnAroundTime);

		$form->setAction($action->view->url($urlParams));
		return $form;
	}
}

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

//	public function getRequiredPermissions()
//	{
//		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CATALOG_ITEM_MODIFY);
//	}

	private function processForm(Form_CatalogItemConfigure $form, $formData, $partnerId, $catalogItemId = null)
	{
		KalturaLog::debug("Got the following Data from the Configure Form:");
		KalturaLog::debug(print_r($formData, true));

		if ($form->isValid($formData))
		{
			$partnerId = $formData['partnerId'];
			if (!$catalogItemId)
				CatalogItemUtils::createNewCatalogItem($formData, $partnerId);
			else
			{
				if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
				elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);
				CatalogItemUtils::updateCatalogItem($partnerId, $catalogItemId, $catalogItem);
			}
			return true;

		} else
		{
			KalturaLog::info('Form was not valid - keep the form open for changing');
			$formData['generalTitle'] = 1; // mark as return from error
			$form->populate($formData);
			return false;
		}

	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$this->client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$request = $action->getRequest();
		$partnerId = $this->_getParam('new_partner_id');

		$catalogItemId = $this->_getParam('catalog_item_id');

		$catalogItemType = $this->_getParam('new_catalog_item_type');
		$cloneTemplateId = $this->_getParam('clone_template_id1');
		$catalogItemForm = null;

		if (!$partnerId)
			$partnerId = 0;

		$type = null;
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			Infra_ClientHelper::impersonate($partnerId);

			if ($cloneTemplateId)
			{
				if ($partnerId)
				{
					$catalogItem = $reachPluginClient->vendorCatalogItem->cloneAction($cloneTemplateId);
					$catalogItemId = $catalogItem->id;
//					$type = $catalogItem->type;
					$type = "Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem";
				} else
				{
					$action->view->errMessage = "Partner ID must be defined.";
					$catalogItemId = null;
					Infra_ClientHelper::unimpersonate();
					return;
				}
			} elseif ($catalogItemId)
			{
				$catalogItem = $reachPluginClient->vendorCatalogItem->get($catalogItemId);
				$type = $catalogItem->type;
			} else
			{
				$type = $this->_getParam('type');
			}

			$form = new Form_CatalogItemConfigure($partnerId, $type);
			$form->populateFromObject($catalogItem, false);

			if (!$form || !($form instanceof Form_CatalogItemConfigure))
			{
				$action->view->errMessage = "Template form not found for type [$type]";
				return;
			}

			$urlParams = array(
				'controller' => 'plugin',
				'action' => 'CatalogItemConfigureAction',
				'clone_template_id1' => null,
			);
			if ($catalogItemId)
				$urlParams['catalog_item_id'] = $catalogItemId;

			$form->setAction($action->view->url($urlParams));

			if ($catalogItemId) // update or clone
			{
				if ($request->isPost())
				{
					$formData = $request->getPost();
					if ($form->isValid($formData))
					{

						$form->populate($formData);
						if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
							$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
						elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
							$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

						$form->resetUnUpdatebleAttributes($catalogItem);
						$catalogItem = $reachPluginClient->vendorCatalogItem->update($catalogItemId, $catalogItem);
						$form->setAttrib('class', 'valid');
						$action->view->formValid = true;
					} else
					{
						$form->populate($formData);
						if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
							$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
						elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
							$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

					}
//					$form->init();
				} else
				{
					$form->populateFromObject($catalogItem);
				}
			} else // new
			{
				$formData = $request->getPost();
				if ($request->isPost() && $form->isValid($request->getPost()))
				{
					if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
					elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

					$form->populate($formData);
					if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
					elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);
					$catalogItem->partnerId = null;
					$catalogItem = $reachPluginClient->vendorCatalogItem->add($catalogItem);
					$form->setAttrib('class', 'valid');
					$action->view->formValid = true;
				} else
				{
					$form->populate($formData);
					if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
					elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);
				}
//				$form->init();
			}
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();

			if ($form)
			{
				$formData = $request->getPost();
				$form->populate($formData);
				if ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::CAPTIONS)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
				elseif ($formData['catalogItemTypeForView'] == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
					$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);
			}
		}
		Infra_ClientHelper::unimpersonate();

		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'CatalogItemConfigureAction',
			'clone_template_id1' => null,
		);
		if ($catalogItemId)
			$urlParams['catalog_item_id'] = $catalogItemId;

		$form->setAction($action->view->url($urlParams));

		$action->view->form = $form;
		$action->view->catalogItemId = $catalogItemId;
	}
}
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

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$this->client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$request = $action->getRequest();
		$partnerId = $this->_getParam('new_partner_id');
		$catalogItemId = $this->_getParam('catalog_item_id');
		$catalogItemType = null;
		$cloneTemplateId = $this->_getParam('clone_template_id');
		$catalogItemForm = null;

		if (!$partnerId)
			$partnerId = 0;

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
					$catalogItemType = $catalogItem->serviceFeature;
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
				$catalogItemType = $catalogItem->serviceFeature;
			} else
			{
				$catalogItemType = $this->_getParam('new_catalog_item_type');
			}

			$form = new Form_CatalogItemConfigure($partnerId, $catalogItemType);

			if (!$form || !($form instanceof Form_CatalogItemConfigure))
			{
				$action->view->errMessage = "Template form not found for type [$catalogItemType]";
				return;
			}

			$urlParams = array(
				'controller' => 'plugin',
				'action' => 'CatalogItemConfigureAction',
				'clone_template_id' => null,
			);
			if ($catalogItemId)
				$urlParams['catalog_item_id'] = $catalogItemId;

			$form->setAction($action->view->url($urlParams));

			if ($catalogItemId) // update or clone
			{
				if ($request->isPost())
				{
					$formData = $request->getPost();
					$form->populate($formData);

					if ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
					elseif ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

					if ($form->isValid($formData))
					{
						$form->resetUnUpdatebleAttributes($catalogItem);
						$catalogItem = $reachPluginClient->vendorCatalogItem->update($catalogItemId, $catalogItem);
						$form->setAttrib('class', 'valid');
						$action->view->formValid = true;
					}
				}else
				{
					$form->populateFromObject($catalogItem, false);
				}
			} else // new
			{
				$formData = $request->getPost();
				$form->populate($formData);
				if ($request->isPost() && $form->isValid($formData))
				{
					if ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS)
					{
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorCaptionsCatalogItem', $formData, false, true);
					}
					elseif ($formData['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
						$catalogItem = $form->getObject('Kaltura_Client_Reach_Type_VendorTranslationCatalogItem', $formData, false, true);

					$form->populate($formData);
					$form->resetUnUpdatebleAttributes($catalogItem);
					$catalogItem = $reachPluginClient->vendorCatalogItem->add($catalogItem);
					$form->setAttrib('class', 'valid');
					$action->view->formValid = true;
				} else
				{
					$form->getElement('partnerId')->setValue($partnerId);
					$form->getElement('serviceFeature')->setValue($catalogItemType);
				}
			}
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();

			if ($form)
			{
				$formData = $request->getPost();
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
}
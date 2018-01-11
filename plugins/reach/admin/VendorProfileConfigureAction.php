<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class VendorProfileConfigureAction extends KalturaApplicationPlugin
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
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$request = $action->getRequest();
		$partnerId = $this->_getParam('new_partner_id');
		$vendorProfileId = $this->_getParam('vendor_profile_id');
		$catalogItemForm = null;

		if (!$partnerId)
			$partnerId = 0;

		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			if ($vendorProfileId)
			{
				$vendorProfile = $reachPluginClient->vendorProfile->get($vendorProfileId);
				$form = new Form_VendorProfileConfigure($partnerId, true);
			} else
			{
				$form = new Form_VendorProfileConfigure($partnerId);
			}

			if (!$form || !($form instanceof Form_VendorProfileConfigure))
			{
				$action->view->errMessage = "Template form not found for type [test]";
				return;
			}

			$urlParams = array(
				'controller' => 'plugin',
				'action' => 'VendorProfileConfigureAction',
			);
			if ($vendorProfileId)
				$urlParams['vendor_profile_id'] = $vendorProfileId;

			$form->setAction($action->view->url($urlParams));

			if ($vendorProfileId) // update
			{
				if ($request->isPost())
				{
					$formData = $request->getPost();
					$form->populate($formData);

					$vendorProfile = $form->getObject('Kaltura_Client_Reach_Type_VendorProfile', $formData, false, true);

					if ($form->isValid($formData))
					{
						$form->resetUnUpdatebleAttributes($vendorProfile);
						$vendorProfile = $reachPluginClient->vendorProfile->update($vendorProfileId, $vendorProfile);
						$form->setAttrib('class', 'valid');
						$action->view->formValid = true;
					}
				}else
				{
					$form->populateFromObject($vendorProfile, false);
				}
			} else // new
			{
				$formData = $request->getPost();
				$form->populate($formData);
				if ($request->isPost() && $form->isValid($formData))
				{
					$vendorProfile = $form->getObject('Kaltura_Client_Reach_Type_VendorProfile', $formData, false, true);

					$form->populate($formData);
					$form->resetUnUpdatebleAttributes($vendorProfile);
					$catalogItem = $reachPluginClient->vendorProfile->add($vendorProfile);
					$form->setAttrib('class', 'valid');
					$action->view->formValid = true;
				} else
				{
					$form->getElement('partnerId')->setValue($partnerId);
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
				$vendorProfile = $form->getObject('Kaltura_Client_Reach_Type_VendorProfile', $formData, false, true);
			}
		}
		Infra_ClientHelper::unimpersonate();

		$action->view->form = $form;
		$action->view->vendorProfileId = $vendorProfileId;
	}
}
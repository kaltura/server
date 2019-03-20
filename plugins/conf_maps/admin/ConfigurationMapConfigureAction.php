<?php
/**
 * @package plugins.confMaps
 * @subpackage Admin
 */
class ConfigurationMapConfigureAction extends KalturaApplicationPlugin
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
		$mapName = $this->_getParam('configuration_map_name');
		$mapHost = $this->_getParam('configuration_map_host');
		$isNew = $this->_getParam('is_new') === 'true' ? true : false;
		$isView = $this->_getParam('is_view') === 'true' ? true : false;
		$version = null;
		if($isView)
		{
			$version = $this->_getParam('configuration_map_version') != "" ? $this->_getParam('configuration_map_version') : null;
		}

		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			if ($isNew)
				$form = $this->handleNewConfigurationItem($action);
			else
				$form = $this->handleExistingConfigurationItem($action, $mapName, $mapHost,$version, $isView);
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			if ($form)
			{
				$formData = $action->getRequest()->getPost();
				$form->populate($formData);
				$form->getObject('Kaltura_Client_ConfMaps_Type_ConfMaps', $formData, false, true);
			}
		}
		$action->view->form = $form;
		$action->view->configurationMapName = $mapName;
		$action->view->configurationMapHost = $mapHost;
	}

	/***
	 * @param $action
	 * @param $configurationMapName
	 * @param $configurationMapHost
	 * @param $version
	 * @param $isView
	 * @return Form_ConfigurationMapConfigure
	 */
	protected function handleExistingConfigurationItem($action, $configurationMapName, $configurationMapHost, $version = null, $isView = false)
	{
		$form = null;
		$request = $action->getRequest();
		$configurationPluginClient = Kaltura_Client_ConfMaps_Plugin::get($this->client);
		$configurationMapFilter = new Kaltura_Client_ConfMaps_Type_ConfMapsFilter();
		$configurationMapFilter->nameEqual= $configurationMapName;
		$configurationMapFilter->relatedHostEqual = $configurationMapHost;
		if ($isView)
		{
			if ($version)
			{
				$configurationMapFilter->versionEqual = $version;
			}

			$configurationMap = $configurationPluginClient->confMaps->get($configurationMapFilter);
			foreach ($configurationMap as $item)
			{
				if (!is_null($item))
				{
					$configurationMap->isEditable = false;
					$form = $this->initForm($action, $configurationMap);
					break;
				}
			}
		}
		else
		{
			$results = $configurationPluginClient->confMaps->listAction($configurationMapFilter);
			foreach ($results->objects as $configurationMap )
			{
				/* @var Kaltura_Client_ConfMaps_Type_ConfigMap $configurationMap */
				if ( $configurationMap->name == $configurationMapName && $configurationMap->relatedHost== $configurationMapHost )
				{
					$form = $this->initForm($action, $configurationMap);
					break;
				}
			}
		}

		if ($form)
		{
			if ($request->isPost())
			{
				$this->handlePost($action, $form, true);
			}
			else
			{
				$content = json_decode($configurationMap->content, true);
				if (!is_null($content))
				{
					$ini = new Zend_Config($content, true);
					$configurationMap->content = $this->iniToString($ini->toArray());
					$form->populateFromObject($configurationMap, false);
				}
				else
				{
					$action->view->errMessage = "Could Not load map for Name [$configurationMapName] and Host [$configurationMapHost]";
				}
			}

		}
		else
		{
			$action->view->errMessage = "Could Not load map for Name [$configurationMapName] and Host [$configurationMapHost]";
		}
		return $form;
	}

	protected function iniToString($ini)
	{
		$res = '';
		foreach ($ini as $key => $value)
		{
			if (is_array($value))
			{
				$res .= "\n[$key]\n" . self::iniToString($value);
			}
			else
			{
				$res .= $key . " = " . (is_numeric($value) ? $value : '"' . $value . '"') . "\n";
			}
		}
		return $res;
	}
	/***
	 * @param $action
	 * @return Form_ConfigurationMapConfigure|null
	 * @throws Zend_Form_Exception
	 */
	protected function handleNewConfigurationItem($action)
	{
		$request = $action->getRequest();
		$form = $this->initForm($action);

		if ($request->isPost())
			$this->handlePost($action, $form);

		return $form;
	}

	/***
	 * @param $action
	 * @param ConfigureForm $form
	 * @param $isUpdate
	 * @throws Zend_Form_Exception
	 */
	protected function handlePost($action, ConfigureForm $form, $isUpdate = false)
	{
		$configurationPluginClient = Kaltura_Client_ConfMaps_Plugin::get($this->client);
		$formData = $action->getRequest()->getPost();
		$form->populate($formData);
		if ($form->isValid($formData))
		{
			$formData['content'] = json_encode(parse_ini_string($formData['content'],true));
			$configurationMap = $form->getObject('Kaltura_Client_ConfMaps_Type_ConfMaps', $formData, false, true);

			$form->resetUnUpdatebleAttributes($configurationMap);
			if($isUpdate)
			{
				$configurationPluginClient->confMaps->update($configurationMap);
			}
			else
			{
				$configurationPluginClient->confMaps->add($configurationMap);
			}
			$form->setAttrib('class', 'valid');
			$action->view->formValid = true;
		}
		else
		{
			$action->view->FormFormat = "invalid";
		}
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param Kaltura_Client_ConfMaps_Type_ConfMaps $configurationMap
	 * @return Form_ConfigurationMapConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $configurationMap = null)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'ConfigurationMapConfigureAction',
		);
		if ($configurationMap)
		{
			$form = new Form_ConfigurationMapConfigure(true, $configurationMap->isEditable);
			if(!$configurationMap->isEditable)
			{
				$action->view->blockSave = true;
			}
			$urlParams['configuration_map_name'] = $configurationMap->name;
			$urlParams['configuration_map_host'] = $configurationMap->relatedHost;

		}
		else
			$form = new Form_ConfigurationMapConfigure();

		$form->setAction($action->view->url($urlParams));
		return $form;
	}
}

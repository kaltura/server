<?php

/**
 * @package plugins.confMaps
 * @subpackage Admin
 */
class ConfigurationMapDiffAction extends KalturaApplicationPlugin
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
		$newContent = $this->_getParam('myContent');
		$mapHost = $this->_getParam('configuration_map_host');
		$version = $this->_getParam('configuration_map_version');

		$action->view->errMessage = null;
		$action->view->form = '';

		try
		{
			$diff = $this->getDiffConfiguration($action, $mapName, $mapHost, $newContent, $version);
			$action->view->form = $diff;
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}

	}

	/**
	 * @param $action
	 * @param $configurationMapName
	 * @param $configurationMapHost
	 * @param null $version
	 * @param $newContent
	 * @return string|null
	 * @throws Kaltura_Client_ClientException
	 * @throws Kaltura_Client_Exception
	 */
	protected function getDiffConfiguration($action, $configurationMapName, $configurationMapHost, $newContent, $version = null)
	{
		$diff = null;
		$configurationPluginClient = Kaltura_Client_ConfMaps_Plugin::get($this->client);
		$configurationMapFilter = new Kaltura_Client_ConfMaps_Type_ConfMapsFilter();
		$configurationMapFilter->nameEqual = $configurationMapName;
		$configurationMapFilter->relatedHostEqual = $configurationMapHost;
		if (!is_null($version))
		{
			$configurationMapFilter->versionEqual = $version;
		}
		$configurationMap = $configurationPluginClient->confMaps->get($configurationMapFilter);
		if ($configurationMap)
		{
			$oldContent = $configurationMap->rawData;
			if (is_null($configurationMap->rawData))
			{
				$mapContentArray = json_decode($configurationMap->content, true);
				if (!empty($mapContentArray))
				{
					$content = IniUtils::arrayToIniString($mapContentArray);
					$oldContent = $content;
				}
			}

			$opcodes = FineDiff::getDiffOpcodes($oldContent, $newContent);
			$diff = '<div id="htmldiff"><div class="pane" style="white-space:pre-wrap">';
			$diff .= FineDiffHTML::renderDiffToHTMLFromOpcodes($oldContent, $opcodes);
			$diff .= '</div></div>';
		}
		else
		{
			$action->view->errMessage = "Could Not Retrieve map for Name [$configurationMapName] and Host [$configurationMapHost] For Diff";
		}
		return $diff;
	}
}

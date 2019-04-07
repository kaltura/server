<?php
/**
 * @package plugins.confMaps
 * @subpackage Admin
 */
class ConfigurationMapListAction extends KalturaApplicationPlugin implements IKalturaAdminConsolePublisherAction
{
	public function __construct()
	{
		$this->action = 'ConfigurationMapListAction';
		$this->label = "Configuration Maps";
		$this->rootLabel = "Configuration";
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
		$configurationMapName = $request->getParam('filter_input');
		$hostName = $this->_getParam('filterHostName') != "" ? $this->_getParam('filterHostName') : null;
		$partnerId = null;

		// init filter
		$configurationMapFilter = $this->getConfigurationMapFilter();
		$configurationMapFilter->orderBy = "-createdAt";

		if($configurationMapName)
		{
			$configurationMapFilter->nameEqual = $configurationMapName;
		}
	    if($hostName)
	    {
		   $configurationMapFilter->relatedHostEqual = $hostName;
	    }

		$client = Infra_ClientHelper::getClient();
		$configurationPluginClient = Kaltura_Client_ConfMaps_Plugin::get($client);

		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($configurationPluginClient->confMaps, "listAction", $partnerId, $configurationMapFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);

		// set view
		$configurationMapFilterForm = new Form_ConfigurationMapFilter();
		$configurationMapFilterForm->populate($request->getParams());
		$configurationMapFilterFormAction = $action->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$configurationMapFilterForm->setAction($configurationMapFilterFormAction);
		$mapNamesValues = array();
		$action->view->newConfigurationMapFolderForm = '';
		try{
			$mapNames = $configurationPluginClient->confMaps->getMapNames();
			foreach ($mapNames as $mapName)
				$mapNamesValues["$mapName->value"] = $mapName->value;
			$configurationMapFilterForm->getElement("filter_input")->setAttrib('options', $mapNamesValues);
			$action->view->filterForm = $configurationMapFilterForm;
			$action->view->paginator = $paginator;

			$createConfigurationMapForm = new Form_CreateConfigurationMap();
			$actionUrl = $action->view->url(array('controller' => 'plugin', 'action' => 'ConfigurationMapConfigure'), null, true);
			$createConfigurationMapForm->setAction($actionUrl);

			$action->view->newConfigurationMapFolderForm = $createConfigurationMapForm;
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getCode();
		}


	}
	
	protected function getConfigurationMapFilter()
	{
		return new Kaltura_Client_ConfMaps_Type_ConfMapsFilter();
	}

	public function getInstance($interface)
	{
		if ($this instanceof $interface)
			return $this;

		return null;
	}

	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName>
	 */
	public function getPublisherAdminActionOptions($partner, $permissions)
	{
		$options = array();
		$options[] = array(0 => 'Configuration', 1 => 'listConfigurationMaps');
		return $options;
	}

	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript()
	{
		$functionStr = 'function listConfigurationMaps(mapName)
		    {
					var url = pluginControllerUrl + \'/' . get_class($this) . '/filter_type/ConfigurationMapNameEqual/filter_input/\' + mapName;
	                document.location = url;
	        }';

		return $functionStr;
	}
}

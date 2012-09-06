<?php
/**
 * @package UI-infra
 */
require_once(dirname(__FILE__) . '/../infra/kConf.php');

/**
 * @package Admin
 */
class InfraBootstrapper extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Run a check to make sure the client existing in the lib directory.
	 * It must be checked before session is initiated, as the session object might contain a class from the client that will cause a fatal error 
	 */
	protected function _initClient()
	{
		$this->bootstrap('autoloaders'); // "autoloaders" is the only bootstrap that is mandatory
		if (!class_exists('Kaltura_Client_Client'))
			throw new Exception('Kaltura client not found, maybe it wasn\'t generated');
	}
	
	protected function _initLog()
	{
		$this->bootstrap('config');
		$this->bootstrap('autoloaders');
		$this->bootstrap('timezone');
		
		$loggerConfigPath = realpath(APPLICATION_PATH . '/../configurations/logger.ini');
		$loggerConfig = new Zend_Config_Ini($loggerConfigPath);
		$configSettings = Zend_Registry::get('config')->settings;
		$loggerName = $configSettings->applicationName;
		$appLogger = $loggerConfig->get($loggerName);
		KalturaLog::initLog($appLogger);
		KalturaLog::debug('starting request');
		
	}	
	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');
	}

	protected function _initPaginator()
	{
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
			'paginator_control.phtml'
		);
	}

	protected function _initNavigation()
	{
		$this->bootstrap('layout');
		$this->bootstrap('acl');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml');

		$navigation = new Zend_Navigation($config);
		
		$baseSettings = Zend_Registry::get('config')->settings;
		if(isset($baseSettings->pluginInterface))
		{
			$pluginInterface = $baseSettings->pluginInterface;
					
			$pluginPages = array();
			$pluginInstances = KalturaPluginManager::getPluginInstances($pluginInterface);
			foreach($pluginInstances as $pluginInstance)
			{
				/* @var $pluginInstance KalturaPlugin */
				KalturaLog::debug("Loading plugin[" . $pluginInstance->getPluginName() . "]");
				foreach($pluginInstance->getApplicationPages() as $pluginPage)
					$pluginPages[] = $pluginPage;
			}
			
			foreach($pluginPages as $pluginPage)
			{
				if(!($pluginPage instanceof KalturaApplicationPlugin))
				{
					KalturaLog::err("Class [" . get_class($pluginPage) . "] is not instance of KalturaApplicationPlugin");
					continue;
				}
				
				$resource = get_class($pluginPage);
				
				$acl = Zend_Registry::get('acl');
				$acl->addResource(new Zend_Acl_Resource($resource));
					
				if(!($pluginPage->accessCheck(Infra_AclHelper::getCurrentPermissions())))
				{
					$acl->deny(Infra_AclHelper::getCurrentRole(), $resource);
					KalturaLog::err("Class [" . get_class($pluginPage) . "] requires permissions [" . print_r($pluginPage->getRequiredPermissions(), true) . "]");
					continue;
				}
				
				$acl->allow(Infra_AclHelper::getCurrentRole(), $resource);				
				
				$menuPage = null;
				
				if($pluginPage->getNavigationRootLabel())
				{
					$menuPage = $navigation->findOneBy('label', $pluginPage->getNavigationRootLabel());
					
					if(!$menuPage)
					{
						$navigation->addPage(array(
							'label' => $pluginPage->getNavigationRootLabel(),
						    'controller' => 'plugin',
							'action' => get_class($pluginPage)));
						
						$menuPage = $navigation->findOneBy('label', $pluginPage->getNavigationRootLabel());
					}
				}
					
				$subMenuPage = null;
				
				if($pluginPage->getNavigationActionLabel())
				{
					$subMenuPage = $navigation->findOneBy('label', $pluginPage->getNavigationActionLabel());
					
					if (!$subMenuPage)
					{
						$navigation->addPage(array(
						    'label' => $pluginPage->getNavigationActionLabel(),
						    'controller' => 'plugin',
							'action' => get_class($pluginPage)));
					}
	
					$subMenuPage = $navigation->findOneBy('label', $pluginPage->getNavigationActionLabel());
				}		
					
				if($menuPage && $subMenuPage)
					$subMenuPage->setParent($menuPage);
			}
		}
		
		$this->checkAclForNavigation($navigation);
			
		$view->navigation($navigation);
	}

	protected function _initAutoloaders()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();

		$moduleAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => dirname(__FILE__),
		));
		$moduleAutoloader->addResourceType('infra', 'Infra', 'Infra');
		$autoloader->pushAutoloader($moduleAutoloader);
		$autoloader->pushAutoloader(new Infra_InfraLoader());
		
		$clientAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH,
		));
		$clientAutoloader->addResourceType('kaltura', 'lib/Kaltura', 'Kaltura');
		$autoloader->pushAutoloader($clientAutoloader);
	}
	
	protected function _initTimeZone()
	{
		date_default_timezone_set(kConf::get('date_default_timezone'));
	}
	
	protected function _initConfig()
	{
	    $this->bootstrap('autoloaders');
	    
		$config = new Zend_Config($this->getOptions(), true);
		$configSettings = $config->settings;
		$configName = $configSettings->applicationName;
		$config = KalturaPluginManager::mergeConfigs($config, $configName, false);		
		Zend_Registry::set('config', $config);
		return $config;
	}
	
	protected function _initLanguage()
	{
	    $translate = Zend_Registry::get(Zend_Application_Resource_Translate::DEFAULT_REGISTRY_KEY);
	    
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationTranslations');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaApplicationTranslations */
			KalturaLog::debug("Loading plugin[" . $pluginInstance->getPluginName() . "]");
			$translations =  $pluginInstance->getTranslations();
			$translate = array_merge($translate, $translations);
		}
		
		Zend_Registry::set(Zend_Application_Resource_Translate::DEFAULT_REGISTRY_KEY, $translate);
	}
	
	protected function _initController()
	{
		$this->bootstrap('acl');
		
		$front = Zend_Controller_Front::getInstance();
		
		$front->registerPlugin(new Infra_AuthPlugin());
		
		$acl = Zend_Registry::get('acl');
		$config = Zend_Registry::get('config');
		$front->registerPlugin(new Infra_ControllerPluginAcl($acl, Infra_AclHelper::getCurrentRole()));
	}
	
	protected function _initAcl()
	{
		$acl = new Zend_Acl();
		
		$acl->addRole(Infra_AclHelper::ROLE_GUEST);
				
		$currentRole = Infra_AclHelper::getCurrentRole();
		$currentPermissions = Infra_AclHelper::getCurrentPermissions();
		
		if (!$acl->hasRole($currentRole)) {
			$acl->addRole($currentRole);
		}
		
      	$accessItems = Zend_Registry::get('config')->access;
      	$allAccess = array();
      	
      	foreach($accessItems as $resource => $accessConfig)
      	{
      		if (!($accessConfig instanceof Zend_Config)) {
      			$requiredPermissions = $accessConfig;
      		}
      		else if (isset($accessConfig->all)) {
      			$requiredPermissions = $accessConfig->all;
      		}
      		else {
      			continue;
      		}
      		
      		$acl->addResource(new Zend_Acl_Resource($resource));
      		
      		if ($requiredPermissions)
      		{
      			$allow = true;
      			if ($requiredPermissions != '*')
      			{
	      			$allAccess[$resource] = $requiredPermissions;
	      			
      				$requiredPermissions = array_map('trim', explode(',', $requiredPermissions));
	      			
	      			foreach ($requiredPermissions as $required) {
	      				if (!in_array($required, $currentPermissions, true)) {
	      					$allow = false;
	      					break;
	      				}
	      			}
      			}
      			
      			if ($allow) {
      				$acl->allow($currentRole, $resource);
      			}
      			else {
      				$acl->deny($currentRole, $resource);
      			}
      		}
      	}
      	
      	foreach($accessItems as $resource => $accessConfig)
      	{      		
      		if ($accessConfig instanceof Zend_Config)
      		{
	      		foreach($accessConfig as $action => $requiredPermissions)
	      		{
	      			if($action == 'all')
	      				continue;
	      		
		      		$acl->addResource(new Zend_Acl_Resource($resource.$action), $resource);
	      				
	      			$allow = true;
	      			if ($requiredPermissions != '*')
		      		{	
		      			if (isset($allAccess[$resource])) {
	      					$requiredPermissions .= ','.$allAccess[$resource];
		      			}
		      			
		      			$requiredPermissions = array_map('trim', explode(',', $requiredPermissions));
	      			
		      			foreach ($requiredPermissions as $required) {
		      				if (!in_array($required, $currentPermissions, true)) {
		      					$allow = false;
		      					break;
		      				}
		      			}
		      		}
		      		else
		      		{
		      		    //If no special permission is required to view this resource, it should be added to the whitelisted resources
    	      			$resourceUrl = "$resource/$action";
    	      			Infra_AuthPlugin::addToWhitelist($resourceUrl);
		      		}
	      			
	      			if ($allow) {
	      				$acl->allow($currentRole, $resource, $action);
	      			}
	      			else {
	      				$acl->deny($currentRole, $resource, $action);
	      			}
	      		}
      		}
      	}
      	
      	
      	Zend_Registry::set('acl', $acl);
	}
		
	
	protected function checkAclForNavigation(Zend_Navigation_Container $navigation)
	{
	    $accessConfig = Zend_Registry::get('config')->access;
		$pages = $navigation->getPages();

		foreach($pages as $page)
		{
			$controller = $page->get('controller');
			$action = $page->get('action');
			$allowed = Infra_AclHelper::isAllowed($controller, $action);

			if(!$allowed)
			{
				$navigation->removePage($page);
			}
			else
			{
				$this->checkAclForNavigation($page);
			}
			if ($action == 'dynamic_action') {
			    $localPages = $page->getPages();
			    $firstPage = reset($localPages);
			    if ($firstPage) {
			        $firstPageAction = $firstPage->get('action');
			        $page->set('action', $firstPageAction);
			    }
			}
		}
	}
}
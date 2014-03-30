<?php
/**
 * @package UI-infra
 */
class InfraBootstrapper extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Static configuration that could be used before loading the config to the registry
	 * @var Zend_Config
	 */
	private static $config = null;

	/**
	 * @return Zend_Config
	 */
	private function getConfig()
	{
		if(!self::$config)
			self::$config = new Zend_Config($this->getOptions(), true);
			
		return self::$config;
	}
	/**
	 * Run a check to make sure the client existing in the lib directory.
	 * It must be checked before session is initiated, as the session object might contain a class from the client that will cause a fatal error 
	 */
	protected function _initClient()
	{
		$this->bootstrap('autoloaders'); // "autoloaders" is the only bootstrap that is mandatory
		if (!class_exists('Kaltura_Client_Client'))
			throw new Infra_Exception('Kaltura client not found, maybe it wasn\'t generated', Infra_Exception::ERROR_CODE_MISSING_CLIENT_LIB);
	}
	
	protected function _initLog()
	{
		$this->bootstrap('autoloaders');
		$this->bootstrap('timezone');
		
		$config = $this->getConfig();
		$configSettings = $config->settings;
		$loggerConfigPath = null;
		if(isset($configSettings->loggerConfigPath))
			$loggerConfigPath = $configSettings->loggerConfigPath;
		else
			$loggerConfigPath = realpath(APPLICATION_PATH . '/../configurations/logger.ini');
			
		$loggerConfig = new Zend_Config_Ini($loggerConfigPath);
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
		$this->bootstrap('log');
		$this->bootstrap('layout');
		$this->bootstrap('acl');
		$this->bootstrap('session');
	    $this->bootstrap('plugins');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml');

		$navigation = new Zend_Navigation($config);
		
		$baseSettings = $this->getConfig()->settings;
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
				
				if(!$pluginPage->isLoginRequired())
					Infra_AuthPlugin::addToWhitelist('plugin/' . $pluginPage->getNavigationActionName());
				
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

	protected function _initPlugins()
	{
		$pluginsConfigPath = null;
		$pluginsCacheNamespace = null;
		
		$config = $this->getConfig();
		if(isset($config->settings->pluginsConfigPath))
			$pluginsConfigPath = $config->settings->pluginsConfigPath;
		if(isset($config->settings->applicationName))
			$pluginsCacheNamespace = $config->settings->applicationName;
			
		KalturaPluginManager::init($pluginsConfigPath, $pluginsCacheNamespace);
	}
	
	protected function _initAutoloaders()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();

		$config = $this->getConfig();
		
		$moduleAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => dirname(__FILE__),
		));
		$moduleAutoloader->addResourceType('infra', 'Infra', 'Infra');
		$autoloader->pushAutoloader($moduleAutoloader);
		$autoloader->pushAutoloader(new Infra_InfraLoader($config->settings));
		
		$clientAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH,
		));
		$clientAutoloader->addResourceType('kaltura', 'lib/Kaltura', 'Kaltura');
		$autoloader->pushAutoloader($clientAutoloader);
	}
	
	protected function _initTimeZone()
	{
		$config = $this->getConfig();
		date_default_timezone_set($config->settings->timeZone);
	}
	
	protected function _initConfig()
	{
	    $this->bootstrap('autoloaders');
		$this->bootstrap('log');
	    $this->bootstrap('plugins');
	    
		$config = $this->getConfig();
		$configSettings = $config->settings;
		$configName = $configSettings->applicationName;
		$config = KalturaPluginManager::mergeConfigs($config, $configName, false);		
		Zend_Registry::set('config', $config);
		return $config;
	}
	
	protected function _initController()
	{
		$this->bootstrap('acl');
		$this->bootstrap('session');
		
		$front = Zend_Controller_Front::getInstance();
		
		$front->registerPlugin(new Infra_AuthPlugin());
		$front->registerPlugin(new Infra_PreventFramesPlugin());
		
		$acl = Zend_Registry::get('acl');
		$config = $this->getConfig();
		$front->registerPlugin(new Infra_ControllerPluginAcl($acl, Infra_AclHelper::getCurrentRole()));
	}
	
	protected function _initSession()
	{
		$this->bootstrap('config');

		$settings = $this->getConfig()->settings;
		$resources = $this->getConfig()->resources;
		$sessionOptions = $settings->sessionOptions;

		$sessionOptionsArray = $sessionOptions->toArray();
		$sessionOptionsArray['cookie_path'] = dirname($resources->frontController->baseurl);

		// Force 'cookie_secure = true' if the request arrived via HTTPS
		if ( $settings->secure_cookie_upon_https )
		{
			$isHttps = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
			if ( $isHttps )
			{
				$sessionOptionsArray['cookie_secure'] = true;
			}
		}

		// Set cookie options
		Zend_Session::setOptions( $sessionOptionsArray );
	}
	
	protected function _initAcl()
	{
		$this->bootstrap('session');
		$this->bootstrap('config');
		$settings = $this->getConfig()->settings;
		if($settings->defaultController)
		{
			if($settings->defaultAction)
				Infra_AuthPlugin::setDefaultAction($settings->defaultController, $settings->defaultAction);
			else
				Infra_AuthPlugin::setDefaultAction($settings->defaultController);
		}
		
		$acl = new Zend_Acl();
		
		$acl->addRole(Infra_AclHelper::ROLE_GUEST);
				
		$currentRole = Infra_AclHelper::getCurrentRole();
		$currentPermissions = Infra_AclHelper::getCurrentPermissions();
		
		if (!$acl->hasRole($currentRole)) {
			$acl->addRole($currentRole);
		}
		
      	$accessItems = $this->getConfig()->access;
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
	    $accessConfig = $this->getConfig()->access;
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
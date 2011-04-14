<?php
require_once(dirname(__FILE__) . '/../alpha/config/kConf.php');

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $startTime;
	
	function __construct($application) 
	{
		$this->startTime = microtime(true);
		parent::__construct($application);
	}
	
	function __destruct() 
	{
		$t = microtime(true) - $this->startTime;
		KalturaLog::debug('boostrap destructed, application run for ' . $t . ' seconds');
	}
	
	protected function _initLog()
	{
		$this->bootstrap('autoloaders');
		$this->bootstrap('config');
		
		$config = Zend_Registry::get('config');
		KalturaLog::initLog($config->logger);
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
		
		$additionalNavigation = Zend_Registry::get('config')->navigation;
		$menu = $additionalNavigation->monitoring;
		$subMenu = $menu->enableDisable;
		
		$target = '';
		if($subMenu->target)
			$target = $subMenu->target;
			
		$navigation->addPage(array(
			    'label' => $subMenu->label,
			    'uri' => $subMenu->uri,
				'target' => $target
		));
		$menuPage = $navigation->findOneBy('label', 'Monitoring');
		$subMenuPage = $navigation->findOneBy('label', $subMenu->label);
		$subMenuPage->setParent($menuPage);
		
		
		$pluginAdminConsolePages = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaAdminConsolePages');
		foreach($pluginInstances as $pluginInstance)
			foreach($pluginInstance->getAdminConsolePages() as $pluginAdminConsolePage)
				$pluginAdminConsolePages[] = $pluginAdminConsolePage;
		
		foreach($pluginAdminConsolePages as $pluginAdminConsolePage)
		{
			if(!($pluginAdminConsolePage instanceof KalturaAdminConsolePlugin))
				continue;
			if(!($pluginAdminConsolePage->accessCheck(Infra_AclHelper::getCurrentPermissions())))
				continue;				
				
			$navigation->addPage(array(
				    'label' => $pluginAdminConsolePage->getNavigationActionLabel(),
				    'controller' => 'plugin',
					'action' => get_class($pluginAdminConsolePage)));
			
			$subMenuPage = $navigation->findOneBy('label', $pluginAdminConsolePage->getNavigationActionLabel());
			$menuPage = null;
			
			if($pluginAdminConsolePage->getNavigationRootLabel())
			{
				$menuPage = $navigation->findOneBy('label', $pluginAdminConsolePage->getNavigationRootLabel());
				
				if(!$menuPage)
				{
					$navigation->addPage(array(
						'label' => $pluginAdminConsolePage->getNavigationRootLabel(),
					    'controller' => 'plugin',
						'action' => get_class($pluginAdminConsolePage)));
					
					$menuPage = $navigation->findOneBy('label', $pluginAdminConsolePage->getNavigationRootLabel());
				}
			}
				
			if($menuPage)
				$subMenuPage->setParent($menuPage);
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
		$moduleAutoloader->addResourceType('infra', '../ui_infra/Infra', 'Infra');
		$autoloader->pushAutoloader($moduleAutoloader);
		
		$clientAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => dirname(__FILE__),
		));
		$clientAutoloader->addResourceType('kaltura', 'lib/Kaltura', 'Kaltura');
		$autoloader->pushAutoloader($clientAutoloader);
		
//		$autoloader->pushAutoloader(new Infra_ClientLoader());
		$autoloader->pushAutoloader(new Infra_InfraLoader());
	}
	
	protected function _initTimeZone()
	{
		$this->bootstrap('config');
		$config = Zend_Registry::get('config');
		date_default_timezone_set($config->settings->timeZone);
	}
	
	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions(), true);
		Zend_Registry::set('config', $config);
		return $config;
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
		}
	}
}
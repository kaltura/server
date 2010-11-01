<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initLog()
	{
		$this->bootstrap('autoloaders');
		$this->bootstrap('config');
		
		$config = Zend_Registry::get('config');
		KalturaLog::initLog($config->logger);
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
			if(!($pluginAdminConsolePage->accessCheck(Kaltura_AclHelper::getCurrentRole())))
				continue;				
				
			$navigation->addPage(array(
				    'label' => $pluginAdminConsolePage->getNavigationActionLabel(),
				    'controller' => 'plugin',
					'action' => get_class($pluginAdminConsolePage)));
			
			if($pluginAdminConsolePage->getNavigationRootLabel())
			{
				$subMenuPage = $navigation->findOneBy('label', $pluginAdminConsolePage->getNavigationActionLabel());
				$menuPage = $navigation->findOneBy('label', $pluginAdminConsolePage->getNavigationRootLabel());
				if($menuPage)
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
		$moduleAutoloader->addResourceType('kaltura', 'lib/Kaltura', 'Kaltura');
		$autoloader->pushAutoloader($moduleAutoloader);
		$autoloader->pushAutoloader(new Kaltura_ClientLoader());
		$autoloader->pushAutoloader(new Kaltura_InfraLoader());
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
		$front->throwExceptions(true);
		
		$front->registerPlugin(new Kaltura_AuthPlugin());
		
		$acl = Zend_Registry::get('acl');
		$config = Zend_Registry::get('config');
		$front->registerPlugin(new Kaltura_ControllerPluginAcl($acl, Kaltura_AclHelper::getCurrentRole()));
	}
	
	protected function _initAcl()
	{
		// roles
		$acl = new Zend_Acl();
		$acl->addRole(Kaltura_AclHelper::ROLE_GUEST)
			->addRole(Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES, Kaltura_AclHelper::ROLE_GUEST)
      		->addRole(Kaltura_AclHelper::ROLE_ADMINISTRATOR, Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES);
      		
      	$accessItems = Zend_Registry::get('config')->access;
      	foreach($accessItems as $resource => $accessConfig)
      	{
      		$acl->add(new Zend_Acl_Resource($resource));
      		
      		$role = Kaltura_AclHelper::ROLE_GUEST;
      		
      		if(!($accessConfig instanceof Zend_Config))
      			$role = $accessConfig;
      		elseif(isset($accessConfig->all))
      			$role = $accessConfig->all;
      			
      		switch($role)
      		{
      			case Kaltura_AclHelper::ROLE_GUEST:
					$acl->allow(null, $resource);
					break;
					
      			case Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES:
					$acl->allow(Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES, $resource);
					$acl->allow(Kaltura_AclHelper::ROLE_ADMINISTRATOR, $resource);
					break;
					
      			case Kaltura_AclHelper::ROLE_ADMINISTRATOR:
					$acl->deny(Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES, $resource);
					$acl->allow(Kaltura_AclHelper::ROLE_ADMINISTRATOR, $resource);
					break;
      		}
      		
      		if($accessConfig instanceof Zend_Config)
      		{
	      		foreach($accessConfig as $action => $role)
	      		{
	      			if($action == 'all')
	      				continue;
	      		
	      			switch($role)
	      			{
	      				case Kaltura_AclHelper::ROLE_GUEST:
							$acl->allow(null, $resource, $action);
							break;
							
	      				case Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES:
							$acl->allow(Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES, $resource, $action);
							break;
							
	      				case Kaltura_AclHelper::ROLE_ADMINISTRATOR:
							$acl->deny(Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES, $resource, $action);
							$acl->allow(Kaltura_AclHelper::ROLE_ADMINISTRATOR, $resource, $action);
							break;
	      			}
	      		}
      		}
      	}
      	Zend_Registry::set('acl', $acl);
	}
	
	protected function checkAclForNavigation(Zend_Navigation_Container $navigation)
	{
		$acl = Zend_Registry::get('acl');
   		$accessConfig = Zend_Registry::get('config')->access;
		$currentRole = Kaltura_AclHelper::getCurrentRole();
		$pages = $navigation->getPages();
		foreach($pages as $page)
		{
			$controller = $page->get('controller');
			if(!isset($accessConfig->$controller))
				continue;
				
			$controllerAccess = $accessConfig->$controller;
			$remove = false;
			
			if($controllerAccess instanceof Zend_Config)
			{
				if(isset($controllerAccess->all))
				{
					if($currentRole == Kaltura_AclHelper::ROLE_GUEST && $controllerAccess->all != Kaltura_AclHelper::ROLE_GUEST)
						$remove = true;
						
					if($currentRole == Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES && $controllerAccess->all == Kaltura_AclHelper::ROLE_ADMINISTRATOR)
						$remove = true;
				}
				
				$action = $page->get('action');
				if(!$remove && isset($controllerAccess->$action))
				{
					$actionAccess = $controllerAccess->$action;
					
					if($currentRole == Kaltura_AclHelper::ROLE_GUEST && $actionAccess != Kaltura_AclHelper::ROLE_GUEST)
						$remove = true;
						
					if($currentRole == Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES && $actionAccess == Kaltura_AclHelper::ROLE_ADMINISTRATOR)
						$remove = true;
				}
			}
			else
			{
				if($currentRole == Kaltura_AclHelper::ROLE_GUEST && $controllerAccess != Kaltura_AclHelper::ROLE_GUEST)
					$remove = true;
					
				if($currentRole == Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES && $controllerAccess == Kaltura_AclHelper::ROLE_ADMINISTRATOR)
					$remove = true;
			}
			
			if($remove)
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
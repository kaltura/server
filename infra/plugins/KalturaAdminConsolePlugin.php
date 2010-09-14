<?php

abstract class KalturaAdminConsolePlugin
{
	/**
	 * @var string - keep null for top level
	 */
	public $rootLabel = null;
	
	/**
	 * @var string - the action name
	 */
	public $action;
	
	/**
	 * @var string - menu label
	 */
	public $label;
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	abstract public function getTemplatePath();
	
	abstract public function doAction(Zend_Controller_Action $action);
	
	abstract public function getRole();
	
	public function accessCheck($currentRole)
	{
		$legalAccess = false;
		
		if ($currentRole == Kaltura_AclHelper::ROLE_ADMINISTRATOR)
		{
			$legalAccess = true;	
		}
		else if ($currentRole == Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES)
		{
			if (($this->getRole() == Kaltura_AclHelper::ROLE_PROFESIONAL_SERVICES) ||
				($this->getRole() == Kaltura_AclHelper::ROLE_GUEST))
				{
					$legalAccess = true;	
				}
		}
		else if ($currentRole == Kaltura_AclHelper::ROLE_GUEST)
		{
			if($this->getRole() == Kaltura_AclHelper::ROLE_GUEST)
			{
				$legalAccess = true;
			}
		}
		return $legalAccess;
	}
	
	public function action(Zend_Controller_Action $action)
	{
		$action->view->addBasePath($this->getTemplatePath());
		$this->doAction($action);
	}
}
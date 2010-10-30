<?php

abstract class KalturaAdminConsolePlugin
{
	/**
	 * @var string - keep null for top level
	 */
	protected $rootLabel = null;
	
	/**
	 * @var string - the action name
	 */
	protected $action = null;
	
	/**
	 * @var string - menu label
	 */
	protected $label = null;
	
	/**
	 * @var Zend_Controller_Action - the executed action
	 */
	private $currentAction = null;
	
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
	
	/**
	 * @return string - keep null for top level
	 */
	public function getNavigationRootLabel()
	{
		return $this->rootLabel;
	}
	
	/**
	 * @return string - the action name
	 */
	public function getNavigationActionName()
	{
		return $this->action;
	}
	
	/**
	 * Return null to exclude from navigation
	 * @return string - menu label
	 */
	public function getNavigationActionLabel()
	{
		return $this->label;
	}
	
	public function action(Zend_Controller_Action $action)
	{
		$this->currentAction = $action;
		$action->view->addBasePath($this->getTemplatePath());
		$this->doAction($action);
	}
	
    protected function _getParam($paramName, $default = null)
    {
        $value = $this->currentAction->getRequest()->getParam($paramName);
        if ((null == $value) && (null !== $default)) {
            $value = $default;
        }

        return $value;
    }
}
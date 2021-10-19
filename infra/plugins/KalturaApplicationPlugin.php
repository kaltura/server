<?php
/**
 * @package infra
 * @subpackage Plugins
 */
abstract class KalturaApplicationPlugin
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
	protected $currentAction = null;
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	abstract public function getTemplatePath();
	
	abstract public function doAction(Zend_Controller_Action $action);
	
	/**
	 * Returns array of required permission names
	 * @return array
	 */
	public function getRequiredPermissions()
	{
		return array();
	}
	
	/**
	 * Indicates that this action requires login
	 * @return boolean
	 */
	public function isLoginRequired()
	{
		return true;
	}
	
	public function accessCheck($currentPermissions)
	{
		$requiredPermissions = $this->getRequiredPermissions();
		foreach ($requiredPermissions as $permission)
		{
			if (!in_array($permission, $currentPermissions))
				return false;
		}
		
		return true;
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

	protected function getFilterFromRequest(Zend_Controller_Request_Abstract $request, $filter)
	{
		$filterInput = $request->getParam('filter_input');

		if(strlen($filterInput))
		{
			$filterType = $request->getParam('filter_type');
			$filter->$filterType = $filterInput;
		}

		return $filter;
	}
}
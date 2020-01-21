<?php
/**
 * @package Admin
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function indexAction()
    {
    	if (Infra_AclHelper::isAllowed('partner', 'list'))
        	$this->_helper->redirector('list', 'partner');
    }
    
    public function loginAction()
    {
    	
    }
    
    public function testmeAction()
    {
    	
    }
    
    public function testmeDocAction()
    {
    	
    }
    
    public function apcAction()
    {
    	
    }
	    
    public function memcacheAction()
    {
    	
    }
	
	public function clientLibsAction()
    {
    	
    }
    
	public function xsdDocAction()
    {
    	
    }
	
	public function kavaAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		if (!isset($settings->kavaDashboard))
		{
			return;
		}
		
		$kavaDashboard = $settings->kavaDashboard;

		$this->view->kavaDashboardUrl = rtrim($kavaDashboard->url, "/") . "/?jwt=" . 
			Form_JwtHelper::getJwt(
				$kavaDashboard->jwtKey, 
				$settings->partnerId, 
				$settings->sessionExpiry);
	}

	public function kelloggsAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		if(!isset($settings->kelloggsDashboard))
		{
			return;
		}

		if (!Infra_AclHelper::isAllowed('developer', 'kelloggs'))
		{
			return;
		}

		$kelloggsDashboard = $settings->kelloggsDashboard;
		$this->view->kelloggsUrl = $kelloggsDashboard->url;
		$this->view->kelloggsServiceUrl = $kelloggsDashboard->serviceUrl;
		$this->view->kelloggsJwt = Form_JwtHelper::getJwt(
			$kelloggsDashboard->jwtKey, 
			$settings->partnerId, 
			$settings->sessionExpiry);
	}

	public function monitoringAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		if (!isset($settings->monitoringDashboard))
		{
			return;
		}
/*
		if (!Infra_AclHelper::isAllowed('developer', 'monitoring'))
		{
			return;
		}
*/
		$monitoringDashboard = $settings->monitoringDashboard;

		$this->view->monitoringDashboardUrl = rtrim($monitoringDashboard->url, "/") . "/?jwt=" .
			Form_JwtHelper::getJwt(
				$monitoringDashboard->jwtKey,
				$settings->partnerId,
				$settings->sessionExpiry);
	}
}
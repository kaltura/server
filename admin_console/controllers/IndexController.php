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
		$sessionExpiry = $settings->sessionExpiry;
		$kavaDashboardUrl = $settings->kavaDashboard->url;
		$jwtKey = $settings->kavaDashboard->jwtKey;
		$partnerId = $settings->partnerId;
		
		if(!$kavaDashboardUrl || !$jwtKey)
			$this->view->kavaDashboardUrl = null;
		else
			$this->view->kavaDashboardUrl = Form_KavaHelper::generateSignedKavaDashboardUrl($kavaDashboardUrl, $jwtKey, $partnerId, $sessionExpiry);
	}
}
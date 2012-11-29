<?php
/**
 * @package KMC
 */
class UserController extends Zend_Controller_Action
{
	public function indexAction() 
	{

	}

	public function login() 
	{
		// Prevent the page fron being embeded in an iframe
		header( 'X-Frame-Options: DENY' );

		// Redirect KMC login to protocol based on settings.secured_login

		// Set initial password / password reminder
		$https_enabled = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;

		$this->view->secured_login = Infra_Config::get('settings.kmc_secured_login') || $https_enabled;
		$this->setPassHashKey = $this->_getParam( "setpasshashkey" );
		$this->view->hashKeyErrorCode = null;
		$this->view->displayErrorFromServer = false;
		if ($this->setPassHashKey) {
			/*
			try {
				$loginData = UserLoginDataPeer::isHashKeyValid($this->setPassHashKey);
				$partnerId = $loginData->getConfigPartnerId();
				$partner = PartnerPeer::retrieveByPK($partnerId);
				if ($partner && $partner->getPasswordStructureValidations())
					$this->displayErrorFromServer = true;  			
				
			}
			catch (kCoreException $e) {
				$this->hashKeyErrorCode = $e->getCode();
			}
			*/
		}
	}

	public function extLoginAction ()
	{   
	    $adapter = new Infra_AuthAdapter();
	    //$adapter->setTimezoneOffset($this->_getParam('timezone_offset'));
	    $adapter->setKS($this->_getParam('ks'));
	    $adapter->setPartnerId($this->_getParam('partner_id'));
		//$adapter = new Zend_Auth_Adapter_DbTable($zendDb);
	    $auth = Infra_AuthHelper::getAuthInstance();
		$result = $auth->authenticate($adapter);
		if ($result->isValid())
		{
			die('Logged in Successfully!');
		}
		else
		{
	         die('Invalid login!');
		}
	}	
}
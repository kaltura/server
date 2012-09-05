<?php
/**
 * @package HostedPages
 * @subpackage User
 */
class UserController extends Zend_Controller_Action
{
    public function loginAction ()
    {
        $loginForm = new Form_Login();
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			$adapter = new Infra_AuthAdapter($request->getPost('email'), $request->getPost('password'), $request->getPost('timezone_offset'), $request->getPost('partner_id'));
			//$adapter = new Zend_Auth_Adapter_DbTable($zendDb);
		    $auth = Infra_AuthHelper::getAuthInstance();
			$result = $auth->authenticate($adapter);
			if ($result->isValid())
			{
			   // Zend_Session::getSaveHandler()->write(uniqid(), $result->getIdentity());
				if ($request->getPost('remember_me'))
					Zend_Session::rememberMe(60*60*24*7); // 1 week
					
				$nextUri = $this->_getParam('next_uri');
				KalturaLog::debug("next uri $nextUri");
				if ($nextUri && strlen($nextUri) > 1)
					$this->_helper->redirector->gotoUrl($nextUri);
				else
					$this->_helper->redirector('list-by-user', 'partner');
			}
			else
			{
		         $loginForm->setDescription('invalid login');
			}
		}
		
		$loginForm->setDefault('next_uri', $this->_getParam('next_uri')); // set in Infra_AuthPlugin
		
		$this->view->loginForm = $loginForm;
		$this->render('login');
    }
    
    public function logoutAction()
	{
		Zend_Session::forgetMe();
		$client = Infra_ClientHelper::getClient();
		$client->session->end();
		Infra_AuthHelper::getAuthInstance()->clearIdentity();
		$this->_helper->redirector('index', 'index');
	}
}


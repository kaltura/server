<?php
class UserController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$filter = new KalturaSystemUserFilter();
		$paginatorAdapter = new Kaltura_FilterPaginator("systemUser", "listAction", $filter);
		$paginator = new Kaltura_Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		$this->view->myEmail = Zend_Auth::getInstance()->getIdentity()->email;
		$this->view->paginator = $paginator;
	}
	
	public function createAction()
	{
		$request = $this->getRequest();
		$newUserForm = new Form_NewUser();
		if ($request->isPost())
		{
			$this->proccessNewUserForm($newUserForm);
		}
		$this->view->newUserForm = $newUserForm;
	}
	
	public function settingsAction()
	{
		$request = $this->getRequest();
		$myInfoForm = new Form_MyInfo();
		if ($request->isPost())
		{
			$this->proccessMyInfoForm($myInfoForm);
		}
		$this->view->myInfoForm = $myInfoForm;
	}
	
	public function resetPasswordAction()
	{
		$request = $this->getRequest();
		$form = new Form_ResetPassword();
		
		if ($request->isPost())
		{
			$client = Kaltura_ClientHelper::getClient();
			$user = $client->systemUser->getByEmail($request->getPost('email'));
			if (!$user)
			{
				$form->setDescription('email not found');
			}
			else if ($user->status != KalturaSystemUserStatus::ACTIVE)
			{
				$form->setDescription('user is not active');
			}
			else
			{
				$array = array(
					'id' => $user->id,
					'expiry' => time() + 60*60*24 // 24 hours
				);
				$config = Zend_Registry::get('config');
				$token = kString::signString(serialize($array), $config->settings->secret);
				$url = $this->view->serverUrl($this->_helper->url('reset-password-link', 'user', null, array('token' => $token)));
				$mailJobData = new KalturaMailJobData();
				$mailJobData->mailType = KalturaMailType::MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD;
				$mailJobData->recipientEmail = $user->email;
				$mailJobData->bodyParams = $url;
				$client->jobs->addMailJob($mailJobData);
				$tranlsate = $this->getFrontController()->getParam('bootstrap')->getResource('translate'); // TODO: add translate action helper
				$form->setDescription(sprintf($tranlsate->_('password instructions emailed to %s'), $user->email));
				$form->hideForm();
			}
		}
		
		$this->view->form = $form;
	}
	
	public function resetPasswordLinkAction()
	{
		$request = $this->getRequest();
		$token = $request->get('token');
		$config = Zend_Registry::get('config');
		$result = kString::crackString($token, $config->settings->secret);
		$array = unserialize($result);
		if (!is_array($array) || !isset($array['id']) || !isset($array['expiry']) || ($array['expiry']) <= time())
		{
			$invalidToken = true;
		}
		else
		{
			$id = $array['id'];
			$client = Kaltura_ClientHelper::getClient();
			$user = $client->systemUser->get($id);
			if (!$user)
				$invalidToken = true;
		}
		
		if ($invalidToken)
			$this->_helper->redirector('reset-password-ok', 'user', null, array('invalid-token' => true));
		
		// create new password
		$newPassword = $client->systemUser->generateNewPassword();
		
		// set the new password
		$client->systemUser->setNewPassword($id, $newPassword);
		
		// email the new password
		$mailJobData = new KalturaMailJobData();
		$mailJobData->mailType = KalturaMailType::MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS;
		$mailJobData->recipientEmail = $user->email;
		$mailJobData->bodyParams = $newPassword;
		$client->jobs->addMailJob($mailJobData);
		
		// redirect to display the message, and to hide the url with the token
		$this->_helper->redirector('reset-password-ok', 'user'); 
	}
	
	public function resetPasswordOkAction()
	{
		$this->view->invalidToken = $this->getRequest()->get('invalid-token');
	}
	
	public function loginAction()
	{
		$loginForm = new Form_Login();
		$resetForm = new Form_ResetPassword();
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			$adapter = new Kaltura_AuthAdapter($request->getPost('email'), $request->getPost('password'));
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($adapter);
			
			if ($result->isValid())
			{
				if ($request->getPost('remember_me'))
					Zend_Session::rememberMe(60*60*24*7); // 1 week
					
				$nextUri = $this->_getParam('next_uri');
				if ($nextUri)
					$this->_helper->redirector->gotoUrl($nextUri);
				else
					$this->_helper->redirector('list', 'partner');
			}
			else
			{
				$loginForm->setDescription('login error');
			}
		}
		
		$loginForm->setDefault('next_uri', $this->_getParam('next_uri')); // set in Kaltura_AuthPlugin
		
		$this->view->loginForm = $loginForm;
		$this->view->resetForm = $resetForm;
		$this->render('login');
	}
	
	public function logoutAction()
	{
		Zend_Session::forgetMe();
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index', 'index');
	}
	
	public function blockAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Kaltura_ClientHelper::getClient();
		$systemUser = new KalturaSystemUser();
		$systemUser->status = KalturaSystemUserStatus::BLOCKED;
		$client->systemUser->update($userId, $systemUser);
		echo $this->_helper->json('ok', false);
	}
	
	public function unblockAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Kaltura_ClientHelper::getClient();
		$systemUser = new KalturaSystemUser();
		$systemUser->status = KalturaSystemUserStatus::ACTIVE;
		$client->systemUser->update($userId, $systemUser);
		echo $this->_helper->json('ok', false);
	}
	
	public function removeAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Kaltura_ClientHelper::getClient();
		$client->systemUser->delete($userId);
		echo $this->_helper->json('ok', false);
	}
	
	private function proccessNewUserForm(Form_NewUser $form)
	{
		$request = $this->getRequest();
		$formData = $request->getPost();
		if ($form->isValid($formData))
		{
			try
			{
				$client = Kaltura_ClientHelper::getClient();
				$systemUser = new KalturaSystemUser();
				$systemUser->email = $request->getPost('email');
				$systemUser->firstName = $request->getPost('first_name');
				$systemUser->lastName = $request->getPost('last_name');
				$systemUser->status = KalturaSystemUserStatus::ACTIVE;
				$systemUser->role = $request->getPost('role');
				$client->systemUser->add($systemUser);
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				if ($ex->getCode() === 'SYSTEM_USER_ALREADY_EXISTS')
					$form->setDescription('user already exists');
				else
					throw $ex;
			}
		}
		else
		{
			$form->populate($formData);
		}
	}
	
	private function proccessMyInfoForm(Form_MyInfo $form)
	{
		$this->view->done = false;
		$request = $this->getRequest();
		$formData = $request->getPost();
		if ($form->isValid($formData))
		{
			$client = Kaltura_ClientHelper::getClient();
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			
			// try to login with the old email and the old password
			$ks = null;
			try
			{
				$validDetails = true;
				$client->systemUser->verifyPassword($identity->email, $request->getPost('old_password'));
			}
			catch(Exception $ex)
			{
				$validDetails = false;
			}
			
			if ($validDetails)
			{
				// now that we know that the old details are correct, we can update its details
				$systemUser = new KalturaSystemUser();
				$systemUser->email = $request->getPost('email_address');
				$systemUser->password = $request->getPost('new_password');
				
				try
				{
					$updatedSystemUser = $client->systemUser->update($identity->id, $systemUser);
					
					$auth->getStorage()->write($updatedSystemUser); // new identity (email could be updated)
					
					$mailJobData = new KalturaMailJobData();
					$mailJobData->mailType = KalturaMailType::MAIL_TYPE_SYSTEM_USER_CREDENTIALS_SAVED;
					$mailJobData->recipientEmail = $updatedSystemUser->email;
					$mailJobData->bodyParams = $systemUser->password;
					$client->jobs->addMailJob($mailJobData);
					
					$this->view->done = true;
				}
				catch(Exception $ex)
				{
					if ($ex->getCode() === 'SYSTEM_USER_ALREADY_EXISTS')
						$form->setDescription('user already exists');
					else
						throw $ex;
				}
			}
			else
			{
				$form->getElement('old_password')
					->addErrorMessage('invalid password')
					->markAsError();
			}
		}
		else
		{
			$form->populate($formData);
		}
	}
}
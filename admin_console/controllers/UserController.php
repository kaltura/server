<?php
class UserController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$filter = new KalturaUserFilter();
		$paginatorAdapter = new Kaltura_FilterPaginator("user", "listAction", $filter);
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
			$userEmail = $request->getPost('email');
			$array = array(
				'email' => $request->getPost('email'),
				'expiry' => time() + 60*60*24 // 24 hours
			);
			$config = Zend_Registry::get('config');
			$token = kString::signString(serialize($array), $config->settings->secret);
			$url = $this->view->serverUrl($this->_helper->url('reset-password-link', 'user', null, array('token' => $token)));
			
			$client->user->resetPassword($array['email']); // ask to reset password
			//TODO: fix it so that reset password link will lead to admin console instead of kmc!
			
			$tranlsate = $this->getFrontController()->getParam('bootstrap')->getResource('translate'); // TODO: add translate action helper
			$form->setDescription(sprintf($tranlsate->_('password instructions emailed to %s'), $request->getPost('email')));
			$form->hideForm();
			
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
		if (!is_array($array) || !isset($array['email']) || !isset($array['expiry']) || ($array['expiry']) <= time())
		{
			$invalidToken = true;
		}
		
		if ($invalidToken)
			$this->_helper->redirector('reset-password-ok', 'user', null, array('invalid-token' => true));
		
		$form = new Form_ResetPasswordLink();
		
		
		if ($request->isPost())
		{
			
			
			// redirect to display the message, and to hide the url with the token
			$this->_helper->redirector('reset-password-ok', 'user');
			$form->hideForm();
		}
		
		//$this->view->form = $form;
		
		
		//TODO: fix it so that reset password link will lead to admin console instead of kmc!
				 
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
		$user = new KalturaUser();
		$user->status = KalturaUserStatus::BLOCKED;
		$client->user->update($userId, $user);
		echo $this->_helper->json('ok', false);
	}
	
	public function unblockAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Kaltura_ClientHelper::getClient();
		$user = new KalturaUser();
		$user->status = KalturaUserStatus::ACTIVE;
		$client->user->update($userId, $user);
		echo $this->_helper->json('ok', false);
	}
	
	public function removeAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Kaltura_ClientHelper::getClient();
		$client->user->delete($userId);
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
				$user = new KalturaUser();
				$user->email = $request->getPost('email');
				$user->firstName = $request->getPost('first_name');
				$user->lastName = $request->getPost('last_name');
				$user->status = KalturaUserStatus::ACTIVE;
				$user->id = $user->email;
				$user->isAdmin = true;
				$partnerData = new Kaltura_AdminConsoleUserPartnerData();
				$partnerData->role = $request->getPost('role');
				$user->partnerData = serialize($partnerData);
				$client->user->add($user);
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				if ($ex->getCode() === 'DUPLICATE_USER_BY_ID')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'PROPERTY_VALIDATION_CANNOT_BE_NULL')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'INVALID_FIELD_VALUE')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'PASSWORD_STRUCTURE_INVALID')
					$form->setDescription($ex->getMessage());
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
			$client->setKs(null);
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			
			try
			{
				$client->user->updateLoginData(
					$identity->email,
					$request->getPost('old_password'),
					$request->getPost('email_address'),
					$request->getPost('new_password')
				);
				
				$ks = $client->user->loginByLoginId($request->getPost('email_address'), $request->getPost('new_password'), Kaltura_ClientHelper::getPartnerId());				
				$client->setKs($ks);
				$user = $client->user->getByLoginId($request->getPost('email_address'), Kaltura_ClientHelper::getPartnerId());
				if (!$user->isAdmin || $user->partnerId != Kaltura_ClientHelper::getPartnerId()) {
					throw new Exception('', 'LOGIN_DATA_NOT_FOUND');
				}
				
				$auth->getStorage()->write($user); // new identity (email could be updated)
				
				$this->view->done = true;
			}
			catch(Exception $ex)
			{
				if ($ex->getCode() === 'LOGIN_DATA_NOT_FOUND') {
					$form->setDescription('user not found');
				}
				else if ($ex->getCode() === 'WRONG_OLD_PASSWORD') {
					$form->getElement('old_password')
					->addErrorMessage('invalid password')
					->markAsError();
					$form->setDescription('old password is wong');
				}
				else if ($ex->getCode() === 'PASSWORD_STRUCTURE_INVALID') {
					$form->setDescription('new password structure is invalid');
				}
				else if ($ex->getCode() === 'PASSWORD_ALREADY_USED') {
					$form->setDescription('password was already used before');
				}
				else if ($ex->getCode() === 'USER_ALREADY_EXISTS') {
					$form->setDescription('new email is already used by a different user');
				}
				else if ($ex->getCode() === 'USER_NOT_FOUND') {
					$form->setDescription('user not found');
				}
				else if ($ex->getCode() === 'INVALID_FIELD_VALUE') {
					$form->setDescription('new email is invalid');
				}
				else {
					throw $ex;
				}				
			}
		}
		else
		{
			$form->populate($formData);
		}
	}

}
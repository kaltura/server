<?php
/**
 * @package Admin
 * @subpackage Users
 */
class UserController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		$client = Infra_ClientHelper::getClient();
		
		// reset form url
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);
		$form = new Form_UserFilter();
		$form->setAction($action);
		
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$userRoles = $client->userRole->listAction();
		Form_PackageHelper::addPackagesToForm($form, $userRoles->objects, 'user_roles', true, 'All Service Editions');
		
		$this->view->partnerPackages = array();
		
		$config = Zend_Registry::get('config');
		
		// init filter
		$userFilter = $this->getUserFilterFromRequest($request);
		$userFilter->partnerIdEqual = $config->settings->partnerId;
		$userFilter->orderBy = Kaltura_Client_Enum_UserOrderBy::CREATED_AT_DESC;
		
		$paginatorAdapter = new Infra_FilterPaginator($client->user, "listAction", null, $userFilter);
		$paginator = new Infra_Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		$this->view->myEmail = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->email;
		$this->view->paginator = $paginator;

							
		// populate the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
	}
	
	private function getUserFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Type_UserFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		$user_roles = $request->getParam('user_roles');
		
		switch ($filterType)
		{
			case "byid":
				$filter->idin = $filterInput;
				break;
			case "byname":
				$filter->screenNameLike = $filterInput;
				break;
			case 'byemail':
				$filter->emailLike = $filterInput;
				break;
		}
		
		if ($user_roles != '')
			$filter->roleIdEqual = $user_roles;
		
		return $filter;
	}
	
	public function createAction()
	{
		$request = $this->getRequest();
		$newUserForm = new Form_NewUser();
		if ($request->isPost())
		{
			$newUserForm->isValid($request->getPost());
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
			$myInfoForm->isValid($request->getPost());
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
			$form->isValid($request->getPost());
			
			$client = Infra_ClientHelper::getClient();
			$userEmail = $request->getPost('email');

			$client->user->resetPassword($userEmail); // ask to reset password
			//TODO: check for exceptions!
			
			$tranlsate = $this->getFrontController()->getParam('bootstrap')->getResource('translate'); // TODO: add translate action helper
			$form->setDescription(sprintf($tranlsate->_('password instructions emailed to %s'), $request->getPost('email')));
			$form->hideForm();
			
		}
		
		$this->view->form = $form;
	}
	
	public function resetPasswordLinkAction()
	{
		$request = $this->getRequest();
		$form = new Form_ResetPasswordLink();
		$token = $request->get('token');
		
		if ($request->isPost())
		{
			$form->isValid($request->getPost());
			$this->proccessResetPasswordLinkForm($form, $token);
		}
		
		$this->view->form = $form;
	}
	
	public function resetPasswordOkAction()
	{
		$this->view->invalidToken = $this->getRequest()->get('invalid-token');
	}
	
	public function loginAction()
	{
		$settings = Zend_Registry::get('config')->settings;
		$redirectUrl = null;

		if(isset($settings->partnerId))
		{
			try
			{
				$client = Infra_ClientHelper::getClient();
				$ssoPlugin = Kaltura_Client_Sso_Plugin::get($client);
				$redirectUrl = $ssoPlugin->sso->login('', 'admin_console', $settings->partnerId);
				if($redirectUrl)
				{
					$this->ssoLogin($settings->partnerId, $client, $redirectUrl);
				}
			}
			catch(Exception $ex)
			{
				// if sso is not set on admin partner try to login using the form
				if ($ex->getCode() !== 'SSO_NOT_FOUND')
				{
					throw $ex;
				}
			}
		}

		$this->formLogin();
	}

	protected function formLogin()
	{
		$loginForm = new Form_Login();
		$resetForm = new Form_ResetPassword();
		$request = $this->getRequest();

		if ($request->isPost())
		{
			$loginForm->isValid($request->getPost());

			$adapter = new Kaltura_AdminAuthAdapter();
			$adapter->setPrivileges('disableentitlement');

			$safeEmailFieldValue = strip_Tags($request->getPost('email')); // Strip HTML Tags to prevent a potential XSS attack
			$passwordFieldValue = $request->getPost('password'); // DO NOT strip 'password' HTML Tags in order not to invalidate passwords (e.g. "<b>BoldPassword</b>")
			$otpFieldValue = $request->getPost ('otp'); //one-time password - might not be provided.

			$adapter->setCredentials($safeEmailFieldValue, $passwordFieldValue, $otpFieldValue);
			$loginForm->getElement('email')->setValue( $safeEmailFieldValue ); // Update the "safe" value onto the form

			$adapter->setTimezoneOffset($request->getPost('timezone_offset'));
			$auth = Infra_AuthHelper::getAuthInstance();
			try
			{
				$result = $auth->authenticate($adapter);
				if ($result->isValid())
				{
					if ($request->getPost('remember_me'))
						Zend_Session::rememberMe(60 * 60 * 24 * 7); // 1 week

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
			catch (Exception $ex)
			{
				$loginForm->setDescription('login error ' . $ex->getMessage());
			}
		}

		$loginForm->setDefault('next_uri', $this->_getParam('next_uri')); // set in Infra_AuthPlugin

		$this->view->loginForm = $loginForm;
		$this->view->resetForm = $resetForm;
		$this->render('login');
	}

	protected function ssoLogin($partnerId, $client, $redirectUrl)
	{
		try
		{
			// if we got session from sso server validate it, if we didnt redirect to sso server
			$ks = $_GET['ks'];
			if($ks)
			{
				$client->setKs($ks);
				$client->user->loginByKs($partnerId);

				$adapter = new Kaltura_AdminAuthAdapter();
				$adapter->setKs($ks);
				$auth = Infra_AuthHelper::getAuthInstance();
				$result = $auth->authenticate($adapter);
				if ($result->isValid())
				{
					$nextUri = $this->_getParam('next_uri');
					if ($nextUri)
						$this->_helper->redirector->gotoUrl($nextUri);
					else
						$this->_helper->redirector('list', 'partner');
				}
				else
				{
					throw new Exception('', 'INVALID_CREDENTIALS');
				}
			}
			else
			{
				$this->getResponse()->setRedirect($redirectUrl);
			}
		}
		catch(Exception $ex)
		{
				throw $ex;
		}
	}
	
	public function logoutAction()
	{
		Zend_Session::forgetMe();
		$client = Infra_ClientHelper::getClient();
		$client->session->end();
		Infra_AuthHelper::getAuthInstance()->clearIdentity();
		$this->_helper->redirector('index', 'index');
	}
	
	public function blockAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Infra_ClientHelper::getClient();
		$user = new Kaltura_Client_Type_User();
		$user->status = Kaltura_Client_Enum_UserStatus::BLOCKED;
		$client->user->update($userId, $user);
		echo $this->_helper->json('ok', false);
	}
	
	public function unblockAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Infra_ClientHelper::getClient();
		$user = new Kaltura_Client_Type_User();
		$user->status = Kaltura_Client_Enum_UserStatus::ACTIVE;
		$client->user->update($userId, $user);
		echo $this->_helper->json('ok', false);
	}
	
	public function removeAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$userId = $this->_getParam('userId');
		$client = Infra_ClientHelper::getClient();
		$client->user->delete($userId);
		echo $this->_helper->json('ok', false);
	}
	
	public function changeRoleAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$userId = $this->_getParam('userId');
		
		$client = Infra_ClientHelper::getClient();
		$user = $client->user->get($userId);
		
		$form = new Form_ChangeUserRole();
		$form->getElement('name')->setValue($user->fullName);
		$form->getElement('email')->setValue($user->email);
		$form->getElement('role')->setValue($user->roleIds);
				
		if ($request->isPost())
		{
			$form->isValid($request->getPost());
			$this->proccessChangeRoleForm($form);
		}
		$this->view->form = $form;
		
	}
	
	public function assignPartnersAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$userId = $this->_getParam('userId');
		
		$client = Infra_ClientHelper::getClient();
		$user = $client->user->get($userId);
		
		$form = new Form_AssignPartners();
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		
		Form_PackageHelper::addPackagesToForm($form, $systemPartnerPlugin->systemPartner->getPackages(), 'partner_package');
		
		$form->getElement('name')->setValue($user->fullName);
		$form->getElement('partners')->setValue($user->allowedPartnerIds);
		$form->getElement('partner_package')->setValue(explode(",",$user->allowedPartnerPackages));
		if ($request->isPost())
		{
			$form->isValid($request->getPost());
			$this->proccessAssignPartnersForm($form);
		}
		$this->view->form = $form;
		
	}
	
	private function proccessNewUserForm(Form_NewUser $form)
	{
		$request = $this->getRequest();
		$formData = $request->getPost();
		if ($form->isValid($formData))
		{
			try
			{
				$client = Infra_ClientHelper::getClient();
				$user = new Kaltura_Client_Type_User();
				$user->email = $request->getPost('email');
				$user->firstName = $request->getPost('first_name');
				$user->lastName = $request->getPost('last_name');
				$user->status = Kaltura_Client_Enum_UserStatus::ACTIVE;
				$user->id = $user->email;
				$user->isAdmin = true;
				$user->roleIds = $request->getPost('role');
				$client->user->add($user);
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				$form->setDescription($ex->getMessage());
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
			$client = Infra_ClientHelper::getClient();
			$client->setKs(null);
			$auth = Infra_AuthHelper::getAuthInstance();
			$identity = $auth->getIdentity();
			
			try
			{
				$client->user->updateLoginData(
					$identity->getUser()->email,
					$request->getPost('old_password'),
					$request->getPost('email_address'),
					$request->getPost('new_password')
				);
				
				$ks = $client->user->loginByLoginId($request->getPost('email_address'), $request->getPost('new_password'), Infra_ClientHelper::getPartnerId(), null, null, null);
				$client->setKs($ks);
				$user = $client->user->getByLoginId($request->getPost('email_address'), Infra_ClientHelper::getPartnerId());
				if ($user->partnerId != Infra_ClientHelper::getPartnerId()) {
					throw new Exception('', 'LOGIN_DATA_NOT_FOUND');
				}
				
				$identity = new Kaltura_AdminUserIdentity($user, $ks);
				$auth->getStorage()->write($identity); // new identity (email could be updated)
				
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
	
	
	private function proccessChangeRoleForm(Form_ChangeUserRole $form)
	{
		$request = $this->getRequest();
		$formData = $request->getPost();
		if ($form->isValid($formData))
		{
			try
			{
				$client = Infra_ClientHelper::getClient();
				
				$userId = $request->getParam('userId');
				$roleId = $request->getPost('role');
				
				$user = new Kaltura_Client_Type_User();
				$user->roleIds = $roleId;
				$client->user->update($userId, $user); // call api user->update
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				$this->view->errMessage = $ex->getMessage();
				if ($ex->getCode() === 'INVALID_USER_ID')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'LOGIN_DATA_NOT_FOUND')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'USER_ROLE_NOT_FOUND')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'NOT_ALLOWED_TO_CHANGE_ROLE')
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
	
	private function proccessAssignPartnersForm(Form_AssignPartners $form)
	{
		$request = $this->getRequest();
		$formData = $request->getPost();
		if ($form->isValid($formData))
		{
			try
			{
				$client = Infra_ClientHelper::getClient();
				
				$userId = $request->getParam('userId');
				$partnerIds = $request->getPost('partners');
				$partnerPackages = $request->getPost('partner_package');
				$partnerPackagesStr = "";
				if (isset($partnerPackages)) {
					$partnerPackagesStr = implode(",", $partnerPackages);
				}
				$user = new Kaltura_Client_Type_User();
				$user->allowedPartnerIds = $partnerIds;
				$user->allowedPartnerPackages = $partnerPackagesStr;
				$client->user->update($userId, $user); // call api user->update
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				if ($ex->getCode() === 'INVALID_USER_ID')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'LOGIN_DATA_NOT_FOUND')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'USER_ROLE_NOT_FOUND')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE')
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
	
	
	public function userRoleAction()
	{
		$client = Infra_ClientHelper::getClient();
			
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$config = Zend_Registry::get('config');
		
		$filter = new Kaltura_Client_Type_UserRoleFilter();
		$filter->partnerIdEqual = $config->settings->partnerId;
		$filter->orderBy = Kaltura_Client_Enum_UserOrderBy::CREATED_AT_DESC;
		$paginatorAdapter = new Infra_FilterPaginator($client->userRole, "listAction", null, $filter);
		$paginator = new Infra_Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		$this->view->myEmail = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->email;
		$this->view->paginator = $paginator;
	}
	
	public function userRoleConfigureAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$userId = $this->_getParam('userId');
		$form = new Form_UserRoleConfiguration();
		$client = Infra_ClientHelper::getClient();
		
		if ($request->isPost())
		{
			$form->isValid($request->getPost());
			$form->populate($request->getPost());
			$userRole = new Kaltura_Client_Type_UserRole();
			$userRole->permissionNames = $form->getPermissionNames();
			try{
				$client->userRole->update($userId, $userRole);
			}
			catch (Exception $e){
				$this->view->errMessage = $e->getMessage();
			}
		}else
		{
			
			$user = $client->userRole->get($userId);
			$form->populateFromObject($user);
		}
		$this->view->form = $form;
	}
	
	
	private function proccessResetPasswordLinkForm($form, $token)
	{
		$request = $this->getRequest();
		$formData = $request->getPost();
		
		if (!$token)
		{
			$form->setDescription('no hash key given');
			return;
		}
		
		if ($form->isValid($formData))
		{
			try
			{
				$newPassword = $userId = $request->getParam('newPassword');
				
				$client = Infra_ClientHelper::getClient();
				$client->setKs(null);
				$client->user->setInitialPassword($token, $newPassword);
				
				$this->_helper->redirector('index');
			}
			catch(Exception $ex)
			{
				if ($ex->getCode() === 'LOGIN_DATA_NOT_FOUND' || $ex->getCode() === 'NEW_PASSWORD_HASH_KEY_INVALID')
					$form->setDescription('reset link is no longer valid');
				else if ($ex->getCode() === 'PASSWORD_STRUCTURE_INVALID')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'NEW_PASSWORD_HASH_KEY_EXPIRED')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'PASSWORD_ALREADY_USED')
					$form->setDescription($ex->getMessage());
				else if ($ex->getCode() === 'INTERNAL_SERVERL_ERROR')
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

}
<?php
/**
 * @package Var
 * @subpackage Partners
 */
class PartnerController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
        $this->_helper->redirector('list');
    }

    public function createAction()
	{
		$request = $this->getRequest();
		$client = Infra_ClientHelper::getClient();
		$form = new Form_PartnerCreate();
		
		$partner = Zend_Registry::get('config')->partner;
		$allowNonePackage = isset($partner->enableNonePackage) ? $partner->enableNonePackage : false;
		
		if ($request->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$partner = $form->getObject("Kaltura_Client_Type_Partner", $request->getPost());
				$templatePartnerId = $form->getValue("copyPartner");
				/* @var $partner Kaltura_Client_Type_Partner */
				if(is_array($partner->contentCategories))
					$partner->contentCategories = implode(',', $partner->contentCategories);
					
				$partner->adminName = $partner->name;
				$partner->description = "Multi-publishers console";
				$partner->type = Kaltura_Client_Enum_PartnerType::ADMIN_CONSOLE;
				
				$result = $client->partner->register($partner, null, $templatePartnerId);
				
				// check for errors in partner.register
				if ($client->isError($result)) 
				{
					if (strpos($result[0]['message'], 'already exists in system') !== false)
						$form->getElement('admin_email')->addError('Email already exists');
					else
						throw new Kaltura_Client_Exception($result[0]['message'], $result[0]['code']);
				}
				else
				{
					Infra_AclHelper::refreshCurrentUserAllowrdPartners();
					$this->_helper->redirector('list');
				}
			}
			else
			{
				$form->populate($request->getPost());
			}
		}
		
		$varConsoleFilter = new Kaltura_Client_VarConsole_Type_VarConsolePartnerFilter();
		$varConsoleFilter->groupTypeEq = Kaltura_Client_Enum_PartnerGroupType::TEMPLATE;
		$pager = new Kaltura_Client_Type_FilterPager();
		$templatePartnerList = $client->partner->listAction($varConsoleFilter, $pager);
		
		$providers = array();
		$providers[0] = "default";
		foreach ($templatePartnerList->objects as $templatePartner)
		{
		    /* @var $templatePartner Kaltura_Client_Type_Partner */
		    $providers[$templatePartner->id] = $templatePartner->name;
		}
		
		$form->setProviders($providers);
		
		$this->view->form = $form;
	}
    
    public function listAction ()
    {
        $request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		// reset form url
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);

		$client = Infra_ClientHelper::getClient();
		
		$form = new Form_PartnerFilter();
		$form->setAction($action);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		$paginatorAdapter = new Infra_FilterPaginator($client->partner, "listAction", null, $partnerFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
		$this->view->paginator = $paginator;
		
    }
    
    private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Type_PartnerFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		$filterIncludActive = $request->getParam('include_active');
		$filterIncludBlocked = $request->getParam('include_blocked');
		$filterIncludRemoved = $request->getParam('include_removed');
		
		if ($filterType == 'byid')
		{
			$filter->idIn = $filterInput;
		}
		else
		{
			if ($filterType == 'byname')
				$filter->nameLike = $filterInput;
			elseif ($filterType == 'free' && $filterInput)
				$filter->partnerNameDescriptionWebsiteAdminNameAdminEmailLike = $filterInput;
		}
		$statuses = array();
		if ($filterIncludActive)
			$statuses[] = Kaltura_Client_Enum_PartnerStatus::ACTIVE;
		if ($filterIncludBlocked)
			$statuses[] = Kaltura_Client_Enum_PartnerStatus::BLOCKED;
		if ($filterIncludRemoved)
			$statuses[] = Kaltura_Client_Enum_PartnerStatus::FULL_BLOCK;
		
		$statusIn = implode(',', $statuses);
		if ($statusIn != ''){
			$filter->statusIn = $statusIn;
		}else{
			$filter->statusIn = Kaltura_Client_Enum_PartnerStatus::ACTIVE . ',' . Kaltura_Client_Enum_PartnerStatus::BLOCKED;
		}
		 
		$filter->orderBy = Kaltura_Client_Enum_PartnerOrderBy::ID_DESC;
		return $filter;
	}
	
    public function updateStatusAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$partnerId = $this->_getParam('partner_id');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$systemPartnerPlugin->systemPartner->updateStatus($partnerId, $status);
		echo $this->_helper->json('ok', false);
	}
	
    public function kmcRedirectAction()
	{
		$partnerId = $this->_getParam('partner_id');
		$userId = $this->_getParam('user_id');
		
		$client = Infra_ClientHelper::getClient();
		
		$client->startMultiRequest();
		
		$currentPartner = $client->partner->getInfo();
		
		if (!$userId)
		{
		    $impersonatedPartner = $client->partner->get($partnerId); 
		    /* @var $impersonatedPartner Kaltura_Client_Type_Partner */
		}
		
		/* @var $currentPartner Kaltura_Client_Type_Partner */
		$client->session->impersonate('{1:result:adminSecret}', $partnerId, $userId ? $userId : '{2:result:adminUserId}', Kaltura_Client_Enum_SessionType::ADMIN, '{1:result:id}', null, "disableentitlement");
		
		$result = $client->doMultiRequest();
		
		$url = null;
		$settings = Zend_Registry::get('config')->settings;
		if($settings->kmcUrl)
		{
			$url = $settings->kmcUrl;
		}
		else
		{
			$url = Infra_ClientHelper::getServiceUrl();	
			$url .= '/index.php/kmc/extlogin';
		}
		// The KS is always the last item received in the multi-request
		$ks = $result[count($result)-1];
		$url .= '?ks='.$ks.'&partner_id='.$partnerId;
		$this->getResponse()->setRedirect($url);
	}
	
	public function varConsoleRedirectAction()
	{
	    $request = $this->getRequest();
		$client = Infra_ClientHelper::getClient();
		$authorizedPartnerId = $this->_getParam('partner_id');
		
		$email = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->email;
		$password = Infra_AuthHelper::getAuthInstance()->getIdentity()->getPassword();
		
	    $adapter = new Infra_AuthAdapter($email, $password);
		$auth = Infra_AuthHelper::getAuthInstance();
		$result = $auth->authenticate($adapter);
		
	    if ($result->isValid())
		{
			$this->_helper->redirector('list', 'partner');
		}
		else
		{
			$loginForm->setDescription('login error');
		}
	}
	
	public function listByUserAction ()
	{
	    $request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		// reset form url
		$action = $this->view->url(array('controller' => $request->getParam('controller'), 'action' => $request->getParam('action')), null, true);

		$client = Infra_ClientHelper::getClient();
		
		$form = new Form_PartnerFilter();
		$form->setAction($action);
		
		
		// get results and paginate
		//$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$paginatorAdapter = new Infra_FilterPaginator($client->partner, "listPartnersForUser", null);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		if ($paginator->getItemsCount() == 1)
		    $this->_helper->redirector('list', 'partner');
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// populate the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
		$this->view->paginator = $paginator;
	}
	
    public function kmcUsersAction()
	{
		$this->_helper->layout->disableLayout();
		
		$partnerId = $this->_getParam('partner_id');
		if (!$partnerId) {
			//TODO: error
		}
		
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$filter = new Kaltura_Client_Type_UserFilter();
		$filter->isAdminEqual = true;
		$filter->partnerIdEqual = $partnerId;
		$filter->statusEqual = Kaltura_Client_Enum_UserStatus::ACTIVE;
		
		$client = Infra_ClientHelper::getClient();
		$paginatorAdapter = new Infra_FilterPaginator($client->user, "listAction", $partnerId, $filter);
		$paginator = new Infra_Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		$paginator->setIndex(2);
		
		$this->view->partnerId = $partnerId;
		$this->view->paginator = $paginator;
	}
}


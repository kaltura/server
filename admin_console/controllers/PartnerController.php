<?php
class PartnerController extends Zend_Controller_Action
{
	const PARTNER_PACKAGE_FREE = 1;
	
	public function indexAction()
	{
		$this->_helper->redirector('list');
	}
	
	public function createAction()
	{
		$request = $this->getRequest();
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$form = new Form_PartnerCreate();
		Form_PackageHelper::addPackagesToForm($form, $systemPartnerPlugin->systemPartner->getPackages());
		
		if ($request->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$partner = $form->getObject("Kaltura_Client_Type_Partner", $request->getPost());
				if(is_array($partner->contentCategories))
					$partner->contentCategories = implode(',', $partner->contentCategories);
					
				$partner->adminName = $partner->name;
				$partner->description = "Admin Console";
				$client->startMultiRequest();
				
				// queue register partner without a ks, otherwise we get an exception saying partner -2 is not a VAR/GROUP
				$originalKs = $client->getKs();
				$client->setKs(null);
				$client->partner->register($partner);
				
				// queue partner package update
				$client->setKs($originalKs);
				$config = new Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration();
				$config->partnerPackage = $form->getValue('partner_package');
				$config->storageDeleteFromKaltura = true;
				$config->storageServePriority = Kaltura_Client_StorageProfile_Enum_StorageServePriority::EXTERNAL_FIRST;
				$systemPartnerPlugin->systemPartner->updateConfiguration('{1:result:id}', $config);
				
				// do multirequest
				$result = $client->doMultiRequest();
				
				// check for errors in partner.register
				if ($client->isError($result[0])) 
				{
					if (strpos($result[0]['message'], 'already exists in system') !== false)
						$form->getElement('admin_email')->addError('Email already exists');
					else
						throw new Kaltura_Client_Exception($result[0]['message'], $result[0]['code']);
				}
				else
				{
					$this->_helper->redirector('list');
				}
			}
			else
			{
				$form->populate($request->getPost());
			}
		}
		
		$this->view->form = $form;
	}
	
	public function listAction()
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
		// if non-commercial partners are not allowed, add to filter
		
		if (Infra_AclHelper::isAllowed('partner','commercial')) {
			$this->view->commercialFiltered = false;
		}
		else {
			$this->view->commercialFiltered = true;
			$partnerFilter->partnerPackageLessThanOrEqual = self::PARTNER_PACKAGE_FREE;
		}
							
		
		// get results and paginate
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		
		$paginatorAdapter = new Infra_FilterPaginator($systemPartnerPlugin->systemPartner, "listAction", null, $partnerFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
		$this->view->paginator = $paginator;
	}
		
	public function updateStorageStatusAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$storageId = $this->_getParam('storageId');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		$client->storageProfile->updateStatus($storageId, $status);
		echo $this->_helper->json('ok', false);
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
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$ks = $systemPartnerPlugin->systemPartner->getAdminSession($partnerId, $userId);

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
		
		$url .= '?ks='.$ks.'&partner_id='.$partnerId;
		$this->getResponse()->setRedirect($url);
	}
	
	public function configureStorageAction()
	{
		$this->_helper->layout->disableLayout();
		$storageId = $this->_getParam('storageId');
		
		$editMode = false;
		if ($storageId)
			$editMode = true;
			
		$client = Infra_ClientHelper::getClient();
		$storageProfilePlugin = Kaltura_Client_StorageProfile_Plugin::get($client);
		
		$form = new Form_Partner_StorageConfiguration();
		Form_Partner_StorageHelper::addProtocolsToForm($form);
		Form_Partner_StorageHelper::addPathManagersToForm($form);
		Form_Partner_StorageHelper::addUrlManagersToForm($form);
		Form_Partner_StorageHelper::addTriggersToForm($form);
		
		$request = $this->getRequest();
		
		
		if (!$editMode) //new
		{
			$flavorParamsResponse = $client->flavorParams->listAction();
			$form->addFlavorParamsFields($flavorParamsResponse);
		}
		else  
		{			
			$storage = $storageProfilePlugin->storageProfile->get($storageId);	
			Infra_ClientHelper::impersonate($storage->partnerId);
			$flavorParamsResponse = $client->flavorParams->listAction();
			Infra_ClientHelper::unimpersonate();
			$flavorParamsIds = array();
			if($storage->flavorParamsIds)
				$flavorParamsIds = explode(',', $storage->flavorParamsIds);
			
			$form->getElement('partnerId')->setAttrib('readonly',true);
			
			$form->addFlavorParamsFields($flavorParamsResponse, $flavorParamsIds);
			
			if (!$request->isPost())
				$form->populateFromObject($storage, false);
		}
		
		$this->view->formValid = true;
		if ($request->isPost())
		{
			$request = $this->getRequest();
			$formData = $request->getPost();
			
			if ($form->isValid($formData))
			{
				KalturaLog::log('Request: ' . print_r($request->getPost(), true));
				$form->populate($request->getPost());
				$storage = $form->getObject("Kaltura_Client_StorageProfile_Type_StorageProfile", $request->getPost(), false, true);
				
				$flavorParams = array();
				foreach($flavorParamsResponse->objects as $flavorParamsItem)
					if($this->_getParam('flavorParamsId_' . $flavorParamsItem->id, false))
						$flavorParams[] = $flavorParamsItem->id;
				
				if(count($flavorParams))
					$storage->flavorParamsIds = implode(',', $flavorParams);
				else		
					$storage->flavorParamsIds = '';

				KalturaLog::log('Storage: ' . print_r($storage, true));
				
				Infra_ClientHelper::impersonate($storage->partnerId);
				$storage->partnerId = null;
				
				if (!$editMode)
				{
					$storageProfilePlugin->storageProfile->add($storage);
				}
				else
				{
					$storageProfilePlugin->storageProfile->update($storageId, $storage);
				}
			}
			else
			{
				$this->view->formValid = false;
				$form->populate($formData);
			}
		}
		
		$this->view->form = $form;
	}
	
	public function configureAction()
	{
		$this->_helper->layout->disableLayout();
		$partnerId = $this->_getParam('partner_id');
		$client = Infra_ClientHelper::getClient();
		$form = new Form_PartnerConfiguration();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		Form_PackageHelper::addPackagesToForm($form, $systemPartnerPlugin->systemPartner->getPackages());
		
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			$form->populate($request->getPost());
			$config = $form->getObject("Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration", $request->getPost());
			$systemPartnerPlugin->systemPartner->updateConfiguration($partnerId, $config);
		}
		else
		{
			$client->startMultiRequest();
			$systemPartnerPlugin->systemPartner->get($partnerId);
			$systemPartnerPlugin->systemPartner->getConfiguration($partnerId);
			$result = $client->doMultiRequest();
			$partner = $result[0];
			$config = $result[1];
			$form->populateFromObject($config);
//			$form->getElement('account_name')->setDescription($partner->name);
			
		}
		
		$this->view->form = $form;
	}
	
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Type_PartnerFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
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
		$statuses[] = Kaltura_Client_Enum_PartnerStatus::ACTIVE;
		$statuses[] = Kaltura_Client_Enum_PartnerStatus::BLOCKED;
		$filter->statusIn = implode(',', $statuses);
		$filter->orderBy = Kaltura_Client_Enum_PartnerOrderBy::ID_DESC;
		return $filter;
	}

	public function externalStoragesAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$client = Infra_ClientHelper::getClient();
		$storageProfilePlugin = Kaltura_Client_StorageProfile_Plugin::get($client);
		$form = new Form_PartnerFilter();
		$newForm = new Form_NewStorage();
		
		$action = $this->view->url(array('controller' => 'partner', 'action' => 'external-storages'), null, true);
		$form->setAction($action);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		// get results and paginate
		
		$paginatorAdapter = new Infra_FilterPaginator($storageProfilePlugin->storageProfile, "listByPartner", null, $partnerFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
		$this->view->newForm = $newForm;
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
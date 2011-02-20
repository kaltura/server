<?php
class PartnerController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->_helper->redirector('list');
	}
	
	public function createAction()
	{
		$request = $this->getRequest();
		$client = Kaltura_ClientHelper::getClient();
		$form = new Form_PartnerCreate();
		Form_PackageHelper::addPackagesToForm($form, $client->systemPartner->getPackages());
		
		if ($request->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$partner = $form->getObject("KalturaPartner", $request->getPost());
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
				$config = new KalturaSystemPartnerConfiguration();
				$config->partnerPackage = $form->getValue('partner_package');
				$config->storageDeleteFromKaltura = true;
				$config->storageServePriority = KalturaStorageServePriority::EXTERNAL_FIRST;
				$client->systemPartner->updateConfiguration('{1:result:id}', $config);
				
				// do multirequest
				$result = $client->doMultiRequest();
				
				// check for errors in partner.register
				if ($client->isError($result[0])) 
				{
					if (strpos($result[0]['message'], 'already exists in system') !== false)
						$form->getElement('admin_email')->addError('Email already exists');
					else
						throw new KalturaException($result[0]['message'], $result[0]['code']);
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

		$client = Kaltura_ClientHelper::getClient();
		
		$form = new Form_PartnerFilter();
		$form->setAction($action);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("systemPartner", "listAction", null, $partnerFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		
		// popule the form
		$form->populate($request->getParams());
		
		// set view
		$this->view->form = $form;
		$this->view->paginator = $paginator;
	}
	
	public function selectorAction()
	{
		$this->listAction();
	}
		
	public function updateStorageStatusAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$storageId = $this->_getParam('storageId');
		$status = $this->_getParam('status');
		$client = Kaltura_ClientHelper::getClient();
		$client->storageProfile->updateStatus($storageId, $status);
		echo $this->_helper->json('ok', false);
	}
	
	public function updateStatusAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$partnerId = $this->_getParam('partner_id');
		$status = $this->_getParam('status');
		$client = Kaltura_ClientHelper::getClient();
		$client->systemPartner->updateStatus($partnerId, $status);
		echo $this->_helper->json('ok', false);
	}
	
	public function kmcRedirectAction()
	{
		$partnerId = $this->_getParam('partner_id');
		$userId = $this->_getParam('user_id');
		$client = Kaltura_ClientHelper::getClient();
		$ks = $client->systemPartner->getAdminSession($partnerId, $userId);

		$url = null;
		$settings = Zend_Registry::get('config')->settings;
		if($settings->kmcUrl)
		{
			$url = $settings->kmcUrl;
		}
		else
		{
			$url = Kaltura_ClientHelper::getServiceUrl();	
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
			
		$client = Kaltura_ClientHelper::getClient();
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
			$storage = $client->storageProfile->get($storageId);	
			Kaltura_ClientHelper::impersonate($storage->partnerId);
			$flavorParamsResponse = $client->flavorParams->listAction();
			Kaltura_ClientHelper::unimpersonate();
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
				$storage = $form->getObject("KalturaStorageProfile", $request->getPost(), false, true);
				
				$flavorParams = array();
				foreach($flavorParamsResponse->objects as $flavorParamsItem)
					if($this->_getParam('flavorParamsId_' . $flavorParamsItem->id, false))
						$flavorParams[] = $flavorParamsItem->id;
				
				if(count($flavorParams))
					$storage->flavorParamsIds = implode(',', $flavorParams);
				else		
					$storage->flavorParamsIds = '';

				KalturaLog::log('Storage: ' . print_r($storage, true));
				
				Kaltura_ClientHelper::impersonate($storage->partnerId);
				$storage->partnerId = null;
				
				if (!$editMode)
				{
					$client->storageProfile->add($storage);
				}
				else
				{
					$client->storageProfile->update($storageId, $storage);
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
		$client = Kaltura_ClientHelper::getClient();
		$form = new Form_PartnerConfiguration();
		Form_PackageHelper::addPackagesToForm($form, $client->systemPartner->getPackages());
		
		
		$moduls = Zend_Registry::get('config')->moduls;
		if ($moduls)
		{
			if (!$moduls->silverLight)
				$form->getElement('enable_silver_light')->setAttrib('disabled',true);
				
			if (!$moduls->liveStream)
				$form->getElement('live_stream_enabled')->setAttrib('disabled',true);
				
			if (!$moduls->vast)
				$form->getElement('enable_vast')->setAttrib('disabled',true);
				
			if (!$moduls->players508)
				$form->getElement('enable508_players')->setAttrib('disabled',true);
				
			if (!$moduls->metadata)
				$form->getElement('enable_metadata')->setAttrib('disabled',true);
				
			if (!$moduls->contentDistribution)
				$form->getElement('enable_content_distribution')->setAttrib('disabled',true);
				
			if (!$moduls->auditTrail)
				$form->getElement('enable_audit_trail')->setAttrib('disabled',true);
			
			if (!$moduls->annotation)
				$form->getElement('enable_annotation')->setAttrib('disabled',false);
		}
		
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			$form->populate($request->getPost());
			$config = $form->getObject("KalturaSystemPartnerConfiguration", $request->getPost());
			$client->systemPartner->updateConfiguration($partnerId, $config);
		}
		else
		{
			$client->startMultiRequest();
			$client->systemPartner->get($partnerId);
			$client->systemPartner->getConfiguration($partnerId);
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
		$filter = new KalturaPartnerFilter();
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
		$statuses[] = KalturaPartnerStatus::ACTIVE;
		$statuses[] = KalturaPartnerStatus::BLOCKED;
		$filter->statusIn = implode(',', $statuses);
		$filter->orderBy = KalturaPartnerOrderBy::ID_DESC;
		return $filter;
	}

	public function externalStoragesAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$client = Kaltura_ClientHelper::getClient();
		$form = new Form_PartnerFilter();
		$newForm = new Form_NewStorage();
		
		$action = $this->view->url(array('controller' => 'partner', 'action' => 'external-storages'), null, true);
		$form->setAction($action);
		
		// init filter
		$partnerFilter = $this->getPartnerFilterFromRequest($request);
		
		// get results and paginate
		$paginatorAdapter = new Kaltura_FilterPaginator("storageProfile", "listByPartner", null, $partnerFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
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
		
		$filter = new KalturaUserFilter();
		$filter->isAdminEqual = true;
		$filter->partnerIdEqual = $partnerId;
		$filter->statusEqual = KalturaUserStatus::ACTIVE;
		$paginatorAdapter = new Kaltura_FilterPaginator("user", "listAction", $partnerId, $filter);
		$paginator = new Kaltura_Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($pageSize);
		$paginator->setIndex(2);
		
		$this->view->partnerId = $partnerId;
		$this->view->paginator = $paginator;
	}
}
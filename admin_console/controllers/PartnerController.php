<?php
/**
 * @package Admin
 * @subpackage Partners
 */
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
		
		$partner = Zend_Registry::get('config')->partner;
		$allowNonePackage = isset($partner->enableNonePackage) ? $partner->enableNonePackage : false;
		
		$client->startMultiRequest();
		$systemPartnerPlugin->systemPartner->getPackages();
		$systemPartnerPlugin->systemPartner->getPackagesVertical();
		$systemPartnerPlugin->systemPartner->getPackagesClassOfService();
		//Retrieve partner 0 template partners.
		$partnerFilter = new Kaltura_Client_SystemPartner_Type_SystemPartnerFilter();
		$partnerFilter->partnerGroupTypeEqual = Kaltura_Client_Enum_PartnerGroupType::TEMPLATE;
		$partnerFilter->partnerParentIdEqual = 0;
		$systemPartnerPlugin->systemPartner->listAction($partnerFilter);
		list($packages, $packagesVertical, $packagesClassOfService, $templatePartners) = $client->doMultiRequest();
		
		if (!(Infra_AclHelper::isAllowed('partner', 'configure-account-packages-service-paid')))
		{
			foreach($packages as $index => $package)
				if(intval($package->id) != PartnerController::PARTNER_PACKAGE_FREE)
					unset($packages[$index]);
		}
		
		Form_PackageHelper::addPackagesToForm($form, $packages,					'partner_package', $allowNonePackage);
		Form_PackageHelper::addPackagesToForm($form, $packagesVertical,			'vertical_clasiffication');
		Form_PackageHelper::addPackagesToForm($form, $packagesClassOfService,	'partner_package_class_of_service');
		Form_PackageHelper::addOptionsToForm($form, $templatePartners->objects, 'partner_template_id', 'name');
		
		//Add languages
		$languages = Zend_Registry::get('config')->languages;
		/* @var $languages Zend_Config */
		Form_PackageHelper::addOptionsToForm($form, $languages, 'partner_language', 'name');
		
		if ($request->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$partner = $form->getObject("Kaltura_Client_Type_Partner", $request->getPost());
				if(is_array($partner->contentCategories))
					$partner->contentCategories = implode(',', $partner->contentCategories);
				/* @var $partner Kaltura_Client_Type_Partner */	
				$partner->adminName = $partner->name;
				$partner->description = "Admin Console";
				$partner->type = Kaltura_Client_Enum_PartnerType::ADMIN_CONSOLE;
				$templatePartnerId = $form->getValue('partner_template_id');
				$client->startMultiRequest();
				KalturaLog::debug("is multi request: ".$client->isMultiRequest());
				$client->partner->register($partner, null, $templatePartnerId);
				$config = new Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration();
				$config->partnerPackage = $form->getValue('partner_package');
				$config->partnerPackageClassOfService = $form->getValue('partner_package_class_of_service');
				$config->verticalClasiffication = $form->getValue('vertical_clasiffication');
				$config->storageDeleteFromKaltura = true;
				$config->storageServePriority = Kaltura_Client_Enum_StorageServePriority::EXTERNAL_FIRST;
				$config->language = $form->getValue('partner_language');
				$systemPartnerPlugin->systemPartner->updateConfiguration('{1:result:id}', $config);
				
				// set request timeout
				$clientConfig = $client->getConfig();
				$clientConfig->curlTimeout = 300;
				$client->setConfig($clientConfig);
				
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
					Kaltura_AdminUserIdentity::refreshCurrentUserAllowedPartners();
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
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$partnerPackages = $systemPartnerPlugin->systemPartner->getPackages();
		Form_PackageHelper::addPackagesToForm($form, $partnerPackages, 'partner_package', true, 'All Service Editions');
		
		if($request->isPost() && $request->getParam('filter_type'))
			$form->isValid($request->getPost());
			
		$this->view->partnerPackages = array();
		foreach($partnerPackages as $package)
		{
			$this->view->partnerPackages[$package->id] = $package->name;
		}
		
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
		
		$plugins = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaAdminConsolePages');
		$partnerActionPluginPages = array();
		foreach($pluginInstances as $pluginInstance)
		{
			$pluginPages = $pluginInstance->getApplicationPages(Infra_AclHelper::getCurrentPermissions());
			foreach ($pluginPages as $pluginPage)
			{
				if ($pluginPage instanceof IKalturaAdminConsolePublisherAction && $pluginPage->accessCheck(Infra_AclHelper::getCurrentPermissions()))
				{
					$partnerActionPluginPages[] = $pluginPage;
				}
			}
		}
		
		$this->view->partnerActionPluginPages = $partnerActionPluginPages;
	}
		
	public function updateStorageStatusAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$partnerId = $this->_getParam('partnerId');
		$storageId = $this->_getParam('storageId');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		Infra_ClientHelper::impersonate($partnerId);
		try
		{
			$client->storageProfile->updateStatus($storageId, $status);
		}
		catch (Exception $e)
		{
			Infra_ClientHelper::unimpersonate();
			throw $e;
		}
		Infra_ClientHelper::unimpersonate();
		
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
		try
		{
			$ks = $systemPartnerPlugin->systemPartner->getAdminSession($partnerId, $userId);
		}
		catch(Exception $e)
		{
			$this->view->partnerId = $partnerId;
			$this->view->errorMessage = $e->getMessage();
			return;
		}

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
		$partnerId = $this->_getParam('partnerId');
		$storageId = $this->_getParam('storageId');
		$type = $this->_getParam('type');
		
		$editMode = false;
		
		$client = Infra_ClientHelper::getClient();
		$storage = null;
		if ($storageId)
		{
			Infra_ClientHelper::impersonate($partnerId);
			try
			{
				$storage = $client->storageProfile->get($storageId);
			}
			catch (Exception $e)
			{
				Infra_ClientHelper::unimpersonate();
				throw $e;
			}
			Infra_ClientHelper::unimpersonate();
			$type = $storage->protocol;
		}
				
		
		$form = KalturaPluginManager::loadObject('Form_Partner_BaseStorageConfiguration', $type, array($partnerId, $type));
		/* @var $form Form_StorageConfiguration */
		
		KalturaLog::debug("form class: ". get_class($form));
		
		if(!$form || !($form instanceof Form_Partner_BaseStorageConfiguration))
		{
			$form = new Form_Partner_StorageConfiguration();
		}
			
		//$form->setAction($action->view->url(array('controller' => 'partner', 'action' => 'configureStorageAction')));
		$request = $this->getRequest();
		$form->populate($request->getParams());
		
		$request = $this->getRequest();
		
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageSize = 500; 
		if (!$storageId) //new
		{
			$partnerId = $request->getParam('new_partner_id');
			$form->getElement('partnerId')->setAttrib('readonly',true);
			$form->getElement('partnerId')->setValue($partnerId);
			
			Infra_ClientHelper::impersonate($partnerId);
			$flavorParamsResponse = $client->flavorParams->listAction(null, $pager);
			Infra_ClientHelper::unimpersonate();
			$form->addFlavorParamsFields($flavorParamsResponse);
		}
		else  
		{			
			$flavorParamsResponse = null;
			Infra_ClientHelper::impersonate($partnerId);
			try
			{
				$flavorParamsResponse = $client->flavorParams->listAction(null, $pager);
			}
			catch (Exception $e)
			{
				Infra_ClientHelper::unimpersonate();
				throw $e;
			}
			Infra_ClientHelper::unimpersonate();
			
			$flavorParamsIds = array();
			if($storage->flavorParamsIds)
				$flavorParamsIds = explode(',', $storage->flavorParamsIds);
			
			$form->getElement('partnerId')->setAttrib('readonly',true);

			$form->addFlavorParamsFields($flavorParamsResponse, $flavorParamsIds);
			
			if (!$request->isPost())
				$form->populateFromObject($storage, false);
		}
		
		
		if ($request->isPost())
		{
			$request = $this->getRequest();
			$formData = $request->getPost();
			
			if ($form->isValid($formData))
			{
				$this->view->formValid = true;
				$form->populate($formData);
				$storageProfileClass = KalturaPluginManager::getObjectClass('Kaltura_Client_Type_StorageProfile', $type);
				
				if (!$storageProfileClass)
				{
					if( $protocol == Kaltura_Client_Enum_StorageProfileProtocol::S3){
						$storageProfileClass = 'Kaltura_Client_Type_AmazonS3StorageProfile';
					}	
					else{
						$storageProfileClass = 'Kaltura_Client_Type_StorageProfile';	
					}
				}
				
				$storageFromForm = $form->getObject($storageProfileClass, $formData, false, true);
				
				$flavorParams = array();
				foreach($flavorParamsResponse->objects as $flavorParamsItem)
					if($this->_getParam('flavorParamsId_' . $flavorParamsItem->id, false))
						$flavorParams[] = $flavorParamsItem->id;
				
				if(count($flavorParams))
					$storageFromForm->flavorParamsIds = implode(',', $flavorParams);
				else		
					$storageFromForm->flavorParamsIds = '';
				
				if (!$editMode)
					$storageFromForm->protocol = $type;
				
				KalturaLog::log('Storage: ' . print_r($storageFromForm, true));
				
				Infra_ClientHelper::impersonate($storageFromForm->partnerId);
				$storageFromForm->partnerId = null;
				if (!$storageId)
				{
					$client->storageProfile->add($storageFromForm);
				}
				else
				{
					$client->storageProfile->update($storageId, $storageFromForm);
				}
			}
			else
			{
				$this->view->formValid = false;
				$form->populate($formData);
			}
		}
		
		KalturaLog::debug("storage protocol: $type");
		$this->view->form = $form;
		$this->view->protocol = $type;
	}
	
	public function configureAction()
	{
		$this->_helper->layout->disableLayout();
		$partnerId = $this->_getParam('partner_id');
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		
		$partner = Zend_Registry::get('config')->partner;
		$allowNonePackage = isset($partner->enableNonePackage) ? $partner->enableNonePackage : false;
		
		$client->startMultiRequest();
		$systemPartnerPlugin->systemPartner->getPackages();
		$systemPartnerPlugin->systemPartner->getPackagesVertical();
		$systemPartnerPlugin->systemPartner->getPackagesClassOfService();
		$systemPartnerPlugin->systemPartner->getPlayerEmbedCodeTypes();
		$systemPartnerPlugin->systemPartner->getPlayerDeliveryTypes();
		list($packages, $packagesVertical, $packagesClassOfService, $playerEmbedCodeTypes, $playerDeliveryTypes) = $client->doMultiRequest();

		$systemDefaults = new stdClass();
		$systemDefaults->id = '';
		$systemDefaults->label = 'Use System Defaults';
		
		$playerEmbedCodeTypes[] = $systemDefaults;
		$playerDeliveryTypes[] = $systemDefaults;
		
		$form = new Form_PartnerConfiguration(array('playerDeliveryTypes' => $playerDeliveryTypes));
		Form_PackageHelper::addPackagesToForm($form, $packages,					'partner_package', $allowNonePackage);
		Form_PackageHelper::addPackagesToForm($form, $packagesVertical,			'vertical_clasiffication');
		Form_PackageHelper::addPackagesToForm($form, $packagesClassOfService,	'partner_package_class_of_service');
		Form_PackageHelper::addOptionsToForm ($form, $playerEmbedCodeTypes,		'default_embed_code_type', 'label');
		Form_PackageHelper::addOptionsToForm ($form, $playerDeliveryTypes,		'default_delivery_type', 'label');
		
		$request = $this->getRequest();
		
		$this->view->errMessage = false;
		if ($request->isPost())
		{
			if ($form->isValid($request->getPost()))
			{	
				$this->view->formValid = true;
				$form->populate($request->getPost());
				$config = $form->getObject("Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration", $request->getPost());
				$config->extendedFreeTrailExpiryDate = strtotime($this->_getParam('extended_free_trail_expiry_date'));
				
				try{
					$systemPartnerPlugin->systemPartner->updateConfiguration($partnerId, $config);
				}
				catch (Exception $e){
					$this->view->errMessage = $e->getMessage();
				}
				
				$extentFreeTrail = $this->_getParam('extended_free_trail');
				
				if (isset($extentFreeTrail) && $extentFreeTrail){
					$status = Kaltura_Client_Enum_PartnerStatus::ACTIVE;
					$client = Infra_ClientHelper::getClient();
					$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
					$systemPartnerPlugin->systemPartner->updateStatus($partnerId, $status);
				}
				
			}else{
				$this->view->formValid = false;
				$form->populate($request->getPost());
			}
		}
		else
		{
			$client->startMultiRequest();
			$systemPartnerPlugin->systemPartner->get($partnerId);
			$systemPartnerPlugin->systemPartner->getConfiguration($partnerId);
			
			try{
				$result = $client->doMultiRequest();
				$partner = $result[0];
				$config = $result[1];
				$form->populateFromObject($config);
			}
			catch (Exception $e){
				$this->view->errMessage = $e->getMessage();
			}
			
			$form->getElement('monitor_usage_history')->setAttrib('onClick', 'openViewHistory('. $partnerId .')');
			$monitorUsageDataElement = $form->getElement('extended_free_trail_expiry_date');
			$monitorUsageDataElement->setValue(date("m/d/y",$monitorUsageDataElement->getValue()));
		}
		
		$this->view->form = $form;
		$this->view->partnerId = $partnerId;
	}
	
	public function extenededFreeTrailHistoryAction()
	{
		$this->_helper->layout->disableLayout();
		$partnerId = $this->_getParam('partner_id');
		$client = Infra_ClientHelper::getClient();
		$form = new Form_ExtenededFreeTrailHistory();
		$auditPlugin = Kaltura_Client_Audit_Plugin::get($client);
			
		$this->view->errMessage = false;

		$client->startMultiRequest();
		$filter = new Kaltura_Client_Audit_Type_AuditTrailFilter();
		$filter->objectIdEqual = $partnerId;
		$filter->auditObjectTypeEqual = "Partner";
		$auditPlugin->auditTrail->listAction($filter);
		
		$extendedFreeTrailHistoryObjects = array();
		try{
			$result = $client->doMultiRequest();
			if (isset($result[0])) {
				foreach($result[0]->objects as $audit) {
					$isExtendedFreeTrailHistory = false;
					foreach($audit->data->changedItems as $changedItem){
						if ($changedItem->descriptor == 'extendedFreeTrailExpiryDate' || $changedItem->descriptor == 'extendedFreeTrailExpiryReason'){ 
					 		$isExtendedFreeTrailHistory = true; 
						 	break;	
					 	}
					}
					if ($isExtendedFreeTrailHistory) {
						$extendedFreeTrailHistoryObjects[] = $audit;
					}
				}
			}
			$this->view->auditList = $extendedFreeTrailHistoryObjects;
		}
		catch (Exception $e){
			$this->view->errMessage = $e->getMessage();
		}
							
		$this->view->history = $extendedFreeTrailHistoryObjects;
		$this->view->form = $form;
		$this->view->partnerId = $partnerId;
	}
	
	
	private function getPartnerFilterFromRequest(Zend_Controller_Request_Abstract $request)
	{
		$filter = new Kaltura_Client_Type_PartnerFilter();
		$filterType = $request->getParam('filter_type');
		$filterInput = $request->getParam('filter_input');
		$filterIncludActive = $request->getParam('include_active');
		$filterIncludBlocked = $request->getParam('include_blocked');
		$filterIncludRemoved = $request->getParam('include_removed');
		$filterPackage = $request->getParam('partner_package');
		
		if (!in_array($filterType,array('','none'))) {
			$_SESSION['partnerLastSearchValue'] = $filterInput;
		}
		
		if($filterType == 'byEntryId') {
			$client = Infra_ClientHelper::getClient();
			$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
			
			try {
				$entry = $adminConsolePlugin->entryAdmin->get($filterInput);
				/* @var $entry Kaltura_Client_Type_MediaEntry */
				$filterInput = $entry->partnerId;
			}
			catch(Exception $ex) {
				$filterInput = "-99";
			}
			$filterType = 'byid';
			
		}
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
		
		if ($filterPackage != '')
			$filter->partnerPackageEqual = $filterPackage;
			
		$statusIn = implode(',', $statuses);
		if ($statusIn != ''){
			$filter->statusIn = $statusIn;
		}else{
			$filter->statusIn = Kaltura_Client_Enum_PartnerStatus::ACTIVE . ',' . Kaltura_Client_Enum_PartnerStatus::BLOCKED;
		}
		 
		$filter->orderBy = Kaltura_Client_Enum_PartnerOrderBy::ID_DESC;
		return $filter;
	}

	public function externalStoragesAction()
	{
		$request = $this->getRequest();
		$page = $this->_getParam('page', 1);
		$pageSize = $this->_getParam('pageSize', 10);
		
		$client = Infra_ClientHelper::getClient();
		$form = new Form_PartnerIdFilter();
		$form->populate($request->getParams());
		$newForm = new Form_NewStorage();
		$newForm->populate($request->getParams());
		
		$action = $this->view->url(array('controller' => 'partner', 'action' => 'external-storages'), null, true);
		$form->setAction($action);
		
		$partnerId = null;
		if ($request->getParam('filter_input') != '') {
			$partnerId = $request->getParam('filter_input');
			$newForm->getElement('newPartnerId')->setValue($partnerId);
		}
		$filter = new Kaltura_Client_Type_StorageProfileFilter();
		
		// get results and paginate
		$paginatorAdapter = new Infra_FilterPaginator($client->storageProfile, "listAction", $partnerId, $filter);
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

	public function resetUserPasswordAction()
	{
		$this->_helper->layout->disableLayout();
		$userId = $this->_getParam('user_id');
		$partnerId = $this->_getParam('partner_id');
		$client = Infra_ClientHelper::getClient();			
		$resetPasswordForm = new Form_Partner_KmcUsersResetPassword();	
		if (!$userId || !$partnerId){
			$this->view->errMessage = "Missing userId/partnerId";
			$this->view->form = $resetPasswordForm;
			return;
		}	
		$request = $this->getRequest();
		//reset button was clicked
		if ($request->isPost())
		{				
			$formData = $request->getPost();
			//password was provided
			if ($resetPasswordForm->isValid($formData))
			{
				$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
				try{
					$systemPartnerPlugin->systemPartner->resetUserPassword($userId, $partnerId, $formData['newPassword']);
					$resetPasswordForm->setAttrib('class', 'valid');					
				}
				catch (Exception $e){
					$this->view->errMessage = $e->getMessage();
				}			
			}
		}
		$this->view->form = $resetPasswordForm;
	}
	
	/**
	 * Multi-Publisher Console redirect
	 */
	public function mpConsoleRedirectAction ()
	{
	    $partnerId = $this->_getParam('partner_id');
		$userId = $this->_getParam('user_id');
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		try
		{
			$ks = $systemPartnerPlugin->systemPartner->getAdminSession($partnerId, $userId);
		}
		catch(Exception $e)
		{
			$this->view->partnerId = $partnerId;
			$this->view->errorMessage = $e->getMessage();
			return;
		}

		$url = null;
		$settings = Zend_Registry::get('config')->settings;
		if($settings->mpConsoleUrl)
		{
			$url = Infra_ClientHelper::getServiceUrl();	
			$url .= $settings->mpConsoleUrl;
		}
		
		$identiry = Infra_AuthHelper::getAuthInstance()->getIdentity();
		/* @var $identiry Infra_UserIdentity */
		
		$formdata = array(
			'ks' => $ks,
			'timezone_offset' => $identiry->getTimezoneOffset(),
		);
		
		$url .= '?' . http_build_query($formdata);
		$this->getResponse()->setRedirect($url);
	}

}
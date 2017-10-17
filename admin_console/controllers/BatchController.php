<?php
/**
 * @package Admin
 * @subpackage Jobs
 */
class BatchController extends Zend_Controller_Action
{

	const BATCH_JOBS_PAGER_SIZE = 500;
	
	public function indexAction()
	{
		$this->_helper->redirector('entry-lifecycle');
	}
	
	public function entryLifecycleAction()
	{
		$request = $this->getRequest();

		$this->view->errors = array();
		$this->view->entry = null;
		$this->view->partner = null;
		
		$action = $this->view->url(array('controller' => 'batch', 'action' => 'entry-lifecycle'), null, true);
		
		$this->view->searchEntryForm = new Form_Batch_SearchEntry();
		$this->view->searchEntryForm->populate($request->getParams());
        $this->view->searchEntryForm->setAction($action);
		
		$entryId = $request->getParam('entryId', false);
		if(!$entryId)
			return;
			
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		if (($request->getParam('searchType') == 'by-flavor-asset-id'))
		{
			try
			{
				// $entryId is actually flavor id in this case
				$entry = $adminConsolePlugin->entryAdmin->getByFlavorId($entryId);
			}
			catch(Exception $e)
			{
				$this->view->errors[] = 'Flavor asset not found: ' . $e->getMessage();
				return;
			}
			$entryId = $entry->id;
		}
		else
		{
			try{
				$entry = $adminConsolePlugin->entryAdmin->get($entryId);
			}
			catch(Exception $e){
				$this->view->errors[] = 'Entry not found: ' . $e->getMessage();
				return;
			}
		}
		$this->view->entry = $entry;
		
		try{
			$this->view->partner = $systemPartnerPlugin->systemPartner->get($entry->partnerId);
		}
		catch(Exception $e){
			$this->view->errors[] = 'Partner not found: ' . $e->getMessage();
		}
		
		$filter = new Infra_BatchJobFilter();
		$filter->entryIdEqual = $entryId;
		$paginatorAdapter = new Infra_FilterPaginator($client->jobs, "listBatchJobs", null, $filter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request, null, 20);
		$paginator->setAction($action);
		
		$this->view->paginator = $paginator;
	}

	private function getDefaultFromDate()
	{
		return time() - (60 * 60 * 12);
	}
	
	private function getDefaultToDate()
	{
		return time() + (60 * 60 * 12);
	}
	
	public function inProgressTasksAction()
	{
		$inProgressStatuses = array(
			Kaltura_Client_Enum_BatchJobStatus::ALMOST_DONE,
			Kaltura_Client_Enum_BatchJobStatus::MOVEFILE,
			Kaltura_Client_Enum_BatchJobStatus::PROCESSED,
			Kaltura_Client_Enum_BatchJobStatus::PROCESSING,
			Kaltura_Client_Enum_BatchJobStatus::QUEUED,
		);
		$inQueueStatuses = array(
			Kaltura_Client_Enum_BatchJobStatus::PENDING,
			Kaltura_Client_Enum_BatchJobStatus::RETRY,
		);
		$defaultJobTypes = array(
			Kaltura_Client_Enum_BatchJobType::CONVERT,
			Kaltura_Client_Enum_BatchJobType::IMPORT,
			Kaltura_Client_Enum_BatchJobType::BULKUPLOAD,
			Kaltura_Client_Enum_BatchJobType::CONVERT_PROFILE,
			Kaltura_Client_Enum_BatchJobType::POSTCONVERT,
			Kaltura_Client_Enum_BatchJobType::EXTRACT_MEDIA,
		);
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_ConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobType');
		$jobTypes = array_flip($oClass->getConstants());
		$jobTypes[Kaltura_Client_Enum_BatchJobType::CONVERT] = $convertSubTypes;
		
		$request = $this->getRequest();
		$action = $this->view->url(array('controller' => 'batch', 'action' => 'in-progress-tasks'), null, true);

		$this->view->errors = array();
		
		$this->view->tasksInProgressForm = new Form_Batch_TasksInProgress();
		$this->view->tasksInProgressForm->populate($request->getParams());
        $this->view->tasksInProgressForm->setAction($action);
		
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$abortJob = $request->getParam('abort', false);
		if ($abortJob)
		{
			Infra_AclHelper::validateAccess('batch', 'in-progress-abort-tasks');
			$abortJobType = $request->getParam('abortType', false);
			try{
				$client->jobs->abortJob($abortJob, $abortJobType);
			}
			catch(Exception $e){
				$this->view->errors[] = 'Error aborting job: ' . $e->getMessage();
			}
		}
		
		
		$filter = new Infra_BatchJobFilter();
		$filter->orderBy = Kaltura_Client_Enum_BatchJobOrderBy::CREATED_AT_DESC;
	
		if($request->getParam('createdAtFrom', false))
		{
			$createdAtFrom = new Zend_Date($this->_getParam('createdAtFrom', $this->getDefaultFromDate()));
			$filter->createdAtGreaterThanOrEqual = $createdAtFrom->toString(Zend_Date::TIMESTAMP);
		}
		else 
		{
	        $createdAtFrom = $this->view->tasksInProgressForm->getElement('createdAtFrom');
	        $createdAtFrom->setValue(date('m/d/Y', $this->getDefaultFromDate()));
			
			$filter->createdAtGreaterThanOrEqual = $this->getDefaultFromDate();
		}
		
		if($request->getParam('createdAtTo', false))
		{
			$createdAtTo = new Zend_Date($this->_getParam('createdAtTo', $this->getDefaultToDate()));
			$createdAtTo->addDay(1);
            $filter->createdAtLessThanOrEqual = $createdAtTo->toString(Zend_Date::TIMESTAMP);
		}
		else
		{
	        $createdAtTo = $this->view->tasksInProgressForm->getElement('createdAtTo');
	        $createdAtTo->setValue(date('m/d/Y', $this->getDefaultToDate()));
	        
			$filter->createdAtLessThanOrEqual = $this->getDefaultToDate();
		}
			
		$entryId = $request->getParam('entryId', null);
		if($entryId && strlen($entryId))
			$filter->entryIdEqual = $entryId;
	
		$partnerId = $request->getParam('partnerId', null);
		if($partnerId && is_numeric($partnerId))
			$filter->partnerIdEqual = $partnerId;
	
//		$allJobs = $request->getParam('allJobs', false);
//		if($allJobs == '0')
//		{
			foreach($jobTypes as $jobType => $jobSubTypes)
			{
				if(is_array($jobSubTypes))
				{
					$inJobSubTypes = array();
		        	foreach($jobSubTypes as $jobSubType)
		        	{
	        			$fieldName = "job_{$jobType}_" . str_replace('.', '_', $jobSubType);
						if($request->getParam($fieldName, false))
							$inJobSubTypes[] = $jobSubType;
		        	}
							
					if(count($inJobSubTypes))
						$filter->addJobType($jobType, $inJobSubTypes);
				}
				else
				{
	        		$fieldName = 'job_' . str_replace('.', '_', $jobType);
					if($request->getParam($fieldName, false))
					{
						$filter->addJobType($jobType);
					}
				}
			}
//		}
//		
//		if(!$allJobs && is_bool($allJobs))
		if(!$filter->hasJobTypeFilter())
		{
			$filter->jobTypeIn = implode(',', $defaultJobTypes);
		}
		
		$inProgressFilter = clone $filter;
		$inProgressFilter->statusIn = implode(',', $inProgressStatuses);
		$paginatorAdapter = new Infra_FilterPaginator($client->jobs, "listBatchJobs", null, $inProgressFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request, 'inProgressPage');
		$paginator->setAction($action);
		$this->view->inProgressPaginator = $paginator;
		
		$inQueueFilter = clone $filter;
		$inQueueFilter->statusIn = implode(',', $inQueueStatuses);
		$paginatorAdapter = new Infra_FilterPaginator($client->jobs, "listBatchJobs", null, $inQueueFilter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request, 'inQueuePage');
		$paginator->setAction($action);
		$this->view->inQueuePaginator = $paginator;
	}
	
	public function failedTasksAction()
	{
		$defaultJobTypes = array(
			Kaltura_Client_Enum_BatchJobType::CONVERT,
			Kaltura_Client_Enum_BatchJobType::IMPORT,
			Kaltura_Client_Enum_BatchJobType::BULKUPLOAD,
		);
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_ConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobType');
		$jobTypes = array_flip($oClass->getConstants());
		$jobTypes[Kaltura_Client_Enum_BatchJobType::CONVERT] = $convertSubTypes;
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobErrorTypes');
		$errorTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobStatus');
		$statuses = array_flip($oClass->getConstants());
		$statuses[Kaltura_Client_Enum_BatchJobStatus::FAILED] = $errorTypes;
		
		$request = $this->getRequest();
		
		$this->view->errors = array();
		
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$submitAction = $request->getParam('submitAction', false);
		if($submitAction && strlen($submitAction))
		{
			$actionJobs = $request->getParam('actionJobs', array());
			$jobsArr = explode(';', $actionJobs);
			$jobs = array();
			foreach($jobsArr as $jobData)
			{
				list($jobId, $jobType) = explode(',', $jobData);
				$jobs[$jobId] = $jobType;
			}
			
			$client->startMultiRequest();
			foreach($jobs as $jobId => $jobType)
			{
				if($submitAction == 'retry') {
					Infra_AclHelper::validateAccess('batch', 'failed-retry-delete');
					$client->jobs->retryJob($jobId, $jobType);
				}
					
				elseif($submitAction == 'delete') {
					Infra_AclHelper::validateAccess('batch', 'failed-retry-delete');
					$client->jobs->deleteJob($jobId, $jobType);
				}
			}
			$results = $client->doMultiRequest();
		}
		
		$action = $this->view->url(array('controller' => 'batch', 'action' => 'failed-tasks'), null, true);

		$this->view->tasksFailedForm = new Form_Batch_TasksFailed();
		
        
		$this->view->tasksFailedForm->populate($request->getParams());
        $this->view->tasksFailedForm->setAction($action);
        
        $submitAction = $this->view->tasksFailedForm->getElement('submitAction');
        $submitAction->setValue('');
		
		$filter = new Infra_BatchJobFilter();
		$filter->orderBy = Kaltura_Client_Enum_BatchJobOrderBy::CREATED_AT_DESC;
	
		if($request->getParam('createdAtFrom', false))
		{
			$createdAtFrom = new Zend_Date($this->_getParam('createdAtFrom', $this->getDefaultFromDate()));
			$filter->createdAtGreaterThanOrEqual = $createdAtFrom->toString(Zend_Date::TIMESTAMP);
		}
		else 
		{
	        $createdAtFrom = $this->view->tasksFailedForm->getElement('createdAtFrom');
	        $createdAtFrom->setValue(date('m/d/Y', $this->getDefaultFromDate()));
			
			$filter->createdAtGreaterThanOrEqual = $this->getDefaultFromDate();
		}
		
		if($request->getParam('createdAtTo', false))
		{
			$createdAtTo = new Zend_Date($this->_getParam('createdAtTo', $this->getDefaultToDate()));
			$filter->createdAtLessThanOrEqual = $createdAtTo->toString(Zend_Date::TIMESTAMP);
		}
		else
		{
	        $createdAtTo = $this->view->tasksFailedForm->getElement('createdAtTo');
	        $createdAtTo->setValue(date('m/d/Y', $this->getDefaultToDate()));
	        
			$filter->createdAtLessThanOrEqual = $this->getDefaultToDate();
		}
	
		$entryId = $request->getParam('entryId', null);
		if($entryId && strlen($entryId))
			$filter->entryIdEqual = $entryId;
	
		$partnerId = $request->getParam('partnerId', null);
		if($partnerId && is_numeric($partnerId))
			$filter->partnerIdEqual = $partnerId;
	
		$inFailedStatuses = array();
		
		if($request->getParam('allReasons', false) == '0')
		{
			$inErrorTypes = array();
			foreach($statuses as $status => $errTypes)
			{
				$statusChecked = $request->getParam("status_$status", false);
				if($statusChecked)
				{
					$inFailedStatuses[] = $status;
				}
				
				if(is_array($errTypes))
				{
		        	foreach($errTypes as $errType)
		        	{
		        		if(!$statusChecked && $request->getParam("status_{$status}_{$errType}", false))
						{
							$inFailedStatuses[] = $status;
							$inErrorTypes[] = $errType;
						}
		        	}
				}
			}
			
			if(count($inErrorTypes))
				$filter->errTypeIn = implode(',', $inErrorTypes);
		}
		
		if(!count($inFailedStatuses))
		{
			$inFailedStatuses = array(
				Kaltura_Client_Enum_BatchJobStatus::FAILED,
				Kaltura_Client_Enum_BatchJobStatus::ABORTED,
				Kaltura_Client_Enum_BatchJobStatus::FATAL,
			);
		}
		
		$filter->statusIn = implode(',', $inFailedStatuses);
		
//		$allJobs = $request->getParam('allJobs', false);
//		if($allJobs == '0')
//		{
			foreach($jobTypes as $jobType => $jobSubTypes)
			{
				if(is_array($jobSubTypes))
				{
					$inJobSubTypes = array();
		        	foreach($jobSubTypes as $jobSubType)
		        	{
	        			$fieldName = 'job_' . str_replace('.', '_', $jobType) . '_' . str_replace('.', '_', $jobSubType);
						if($request->getParam($fieldName, false))
							$inJobSubTypes[] = $jobSubType;
		        	}
							
					if(count($inJobSubTypes))
						$filter->addJobType($jobType, $inJobSubTypes);
				}
				else
				{
	        		$fieldName = 'job_' . str_replace('.', '_', $jobType);
					if($request->getParam($fieldName, false))
					{
						$filter->addJobType($jobType);
					}
				}
			}
//		}
	
//		if(!$allJobs && is_bool($allJobs))
		if(!$filter->hasJobTypeFilter())
			$filter->jobTypeIn = implode(',', $defaultJobTypes);
		
		$paginatorAdapter = new Infra_FilterPaginator($client->jobs, "listBatchJobs", null, $filter);
		$paginator = new Infra_Paginator($paginatorAdapter, $request);
		$paginator->setAction($action);
		$this->view->paginator = $paginator;
	}
	
	public function setupAction()
	{
		$request = $this->getRequest();
		
		$this->view->errors = array();
		$this->view->schedulers = array();
		$this->view->workers = array();
		
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$adminId = Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->id;
		
		$action = $request->getParam('hdnAction', false);
		if($action)
		{
			Infra_AclHelper::validateAccess('batch', 'setup-stop-start');
			$workerId = $request->getParam('hdnWorkerId', false);
			$cause = $request->getParam('hdnCause', false);

			try{
				switch($action)
				{
					case "stop":
						$client->batchcontrol->stopWorker($workerId, $adminId, $cause);
						$this->view->actionDescription .= "Worker [$workerId] stop command sent.\n";
						break;
						
					case "start":
						$client->batchcontrol->startWorker($workerId, $adminId, $cause);
						$this->view->actionDescription .= "Worker [$workerId] start command sent.\n";
						break;
						
					case "disable":
						$configParam = 'enable';
						$configValue = '0';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						$this->view->actionDescription .= "Worker [$workerId] disable command sent.\n";
						break;
						
					case "enable":
						$configParam = 'enable';
						$configValue = '1';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						$this->view->actionDescription .= "Worker [$workerId] enable command sent.\n";
						break;
						
					case "start-manual":
						$configParam = 'enable';
						$configValue = '1';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						
						$configParam = 'autoStart';
						$configValue = '0';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						
						$this->view->actionDescription .= "Worker [$workerId] start manualy command sent.\n";
						break;
						
					case "start-auto":
						$configParam = 'enable';
						$configValue = '1';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						
						$configParam = 'autoStart';
						$configValue = '1';
						$configParamPart = null;
						$client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
						
						$this->view->actionDescription .= "Worker [$workerId] start automatic command sent.\n";
						break;
				}
			}
			catch(Exception $e){
				$this->view->errors[] = $e->getMessage();
			}
		}
	
		$filter = new Kaltura_Client_Type_ControlPanelCommandFilter();
		$filter->createdByIdEqual = $adminId;
		$filter->statusIn = Kaltura_Client_Enum_ControlPanelCommandStatus::HANDLED . ',' . Kaltura_Client_Enum_ControlPanelCommandStatus::PENDING;
		
		$this->view->disabledWorkers = array();
		try{
			$commandsList = $client->batchcontrol->listCommands($filter);
			
			foreach($commandsList->objects as $command)
				if($command->type != Kaltura_Client_Enum_ControlPanelCommandType::CONFIG)
					$this->view->disabledWorkers[$command->workerId] = $command;
		}
		catch(Exception $e){
			$this->view->errors[] = $e->getMessage();
		}
		
		
		$settings = Zend_Registry::get('config')->settings;
		$controlCommandsTimeFrame = $settings->controlCommandsTimeFrame * 60;
		
		$filter = new Kaltura_Client_Type_ControlPanelCommandFilter();
		$filter->createdByIdEqual = $adminId;
		$filter->createdAtGreaterThanOrEqual = (time() - $controlCommandsTimeFrame) ;
		
		try{
			$commandsList = $client->batchcontrol->listCommands($filter);
			$this->view->commands = $commandsList->objects;
		}
		catch(Exception $e){
			$this->view->commands = array();
		}
		
		try{
			$schedulersList = $client->batchcontrol->listSchedulers();
			$this->view->schedulers = $schedulersList->objects;
		}
		catch(Exception $e){
			$this->view->schedulers = array();
		}
		
		try{
			$workersList = $client->batchcontrol->listWorkers();
			$this->view->workers = $workersList->objects;
		}
		catch(Exception $e){
			$this->view->workers = array();
		}
	}

	
	public function importInvestigationAction() 
	{
	}

	
	public function exportInvestigationAction() 
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = $this->getRequest();
		
		$entryId = $request->getParam('entryId', false);
		if(!$entryId)
			return;
	
		$fileName = "Entry_$entryId.ked";
		header('Content-type: text/ked');
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		
		$errors = array();
		$investigateData = $this->getEntryInvestigationData($entryId, $errors);
		$content = base64_encode(serialize($investigateData));
		echo $content;
		
		$requestAction = $request->getParam('requestAction', false);
		if($requestAction && $requestAction == 'requestSupport')
		{
			$host = $_SERVER['HTTP_HOST'];
			$summary = "Admin console supoport request from $host";
			$description = $request->getParam('supportDescription', '');
			$ticketId = Infra_Support::addIssue($summary, $description, $content);
			if(!$ticketId)
				$errors[] = "Support ticket could not be send";
		}
	}
	
	/**
	 * @param string $entryId
	 * @param array $errors
	 * @return Kaltura_Client_Enum_InvestigateEntryData
	 */
	public function getEntryInvestigationData($entryId, &$errors)
	{
		$investigateData = new Kaltura_Client_AdminConsole_Type_InvestigateEntryData();
	
		$client = Infra_ClientHelper::getClient();
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		$fileSyncConsolePlugin = Kaltura_Client_FileSync_Plugin::get($client);
		
		if(!$client)
		{
			$errors[] = 'init client failed';
			return;
		}
		
		try{
			$entry = $adminConsolePlugin->entryAdmin->get($entryId);
		}
		catch(Exception $e){
			$errors[] = 'Entry not found: ' . $e->getMessage();
			return;
		}
		$investigateData->entry = $entry;
		
		try{
			$trackList = $adminConsolePlugin->entryAdmin->getTracks($entryId);
			$investigateData->tracks = $trackList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Tracks not found: ' . $e->getMessage();
			$investigateData->tracks = array();
		}
		
		$filter = new Infra_BatchJobFilter();
		$filter->entryIdEqual = $entryId;
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageSize = self::BATCH_JOBS_PAGER_SIZE;
		try{
			$jobsList = $client->jobs->listBatchJobs($filter,$pager);
			$investigateData->jobs = $jobsList;
		}
		catch(Exception $e){
			$errors[] = 'Jobs not found: ' . $e->getMessage();
		}
		
		if($entry->status == Kaltura_Client_Enum_EntryStatus::DELETED)
		{
			$investigateData->fileSyncs = array();
			$investigateData->flavorAssets = array();
			return $investigateData;
		}
		
		$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
		$filter->objectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::ENTRY;
		$filter->objectIdEqual = $entryId;
		try{
			$filesList = $fileSyncConsolePlugin->fileSync->listAction($filter);
			$investigateData->fileSyncs = $filesList;
		}
		catch(Exception $e){
			$errors[] = 'Template Flavor Params not found: ' . $e->getMessage();
		}
		
		$flavorParams = array();
		$thumbParams = array();
		$templateFlavorParams = null;
		$templateThumbParams = null;
		$partnerFlavorParams = null;
		$partnerThumbParams = null;
		
		try{
			$templateFlavorParamsList = $client->flavorParams->listAction();
			$templateFlavorParams = $templateFlavorParamsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Template Flavor Params not found: ' . $e->getMessage();
		}
	
		try{
			$templateThumbParamsList = $client->thumbParams->listAction();
			$templateThumbParams = $templateThumbParamsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Template Thumb Params not found: ' . $e->getMessage();
		}
	
		Infra_ClientHelper::impersonate($entry->partnerId);
		
		try{
			$partnerFlavorParamsList = $client->flavorParams->listAction();
			$partnerFlavorParams = $partnerFlavorParamsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Partner Flavor Params not found: ' . $e->getMessage();
		}
	
		try{
			$partnerThumbParamsList = $client->thumbParams->listAction();
			$partnerThumbParams = $partnerThumbParamsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Partner Thumb Params not found: ' . $e->getMessage();
		}
	
		if(is_array($templateFlavorParams) && count($templateFlavorParams))
		{
			foreach($templateFlavorParams as $param)
				$flavorParams[$param->id] = $param;
		}
		if(is_array($partnerFlavorParams) && count($partnerFlavorParams))
		{
			foreach($partnerFlavorParams as $param)
				$flavorParams[$param->id] = $param;
		}
	
		if(is_array($templateThumbParams) && count($templateThumbParams))
		{
			foreach($templateThumbParams as $param)
				$thumbParams[$param->id] = $param;
		}
		if(is_array($partnerThumbParams) && count($partnerThumbParams))
		{
			foreach($partnerThumbParams as $param)
				$thumbParams[$param->id] = $param;
		}
		
		$flavors = null;
		try{
			$filter = new Kaltura_Client_Type_AssetFilter();
			$filter->entryIdEqual = $entryId;
			$flavorsList = $client->flavorAsset->listAction($filter);
			$flavors = $flavorsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Flavors not found: ' . $e->getMessage();
		}
		
		$thumbs = null;
		try{
			$filter = new Kaltura_Client_Type_AssetFilter();
			$filter->entryIdEqual = $entryId;
			$thumbsList = $client->thumbAsset->listAction($filter);
			$thumbs = $thumbsList->objects;
		}
		catch(Exception $e){
			$errors[] = 'Thumbs not found: ' . $e->getMessage();
		}
		
		Infra_ClientHelper::unimpersonate();
		
		$flavorsData = array();
		if(is_array($flavors) && count($flavors))
		{
			foreach($flavors as $flavor)
			{
				$flavorData = new Kaltura_Client_AdminConsole_Type_InvestigateFlavorAssetData();
				$flavorData->flavorAsset = $flavor;
				$flavorData->flavorParams = null;
				$flavorData->flavorParamsOutputs = array();
				$flavorData->mediaInfos = array();
				$flavorData->fileSyncs = array();
				
				if(isset($flavorParams[$flavor->flavorParamsId]))
					$flavorData->flavorParams = $flavorParams[$flavor->flavorParamsId];
			
				$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
				$filter->objectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET;
				$filter->objectIdEqual = $flavor->id;
				try{
					$filesList = $fileSyncConsolePlugin->fileSync->listAction($filter);
					$flavorData->fileSyncs = $filesList;
				}
				catch(Exception $e){
					$errors[] = "Flavor [$flavor->id] files not found: " . $e->getMessage();
				}
			
				$filter = new Kaltura_Client_Type_FlavorParamsOutputFilter();
				$filter->flavorAssetIdEqual = $flavor->id;
				try{
					$flavorParamsOutputsList = $client->flavorParamsOutput->listAction($filter);
					$flavorData->flavorParamsOutputs = $flavorParamsOutputsList;
				}
				catch(Exception $e){
					$errors[] = "Flavor [$flavor->id] flavor params outputs not found: " . $e->getMessage();
				}
				
				$filter = new Kaltura_Client_Type_MediaInfoFilter();
				$filter->flavorAssetIdEqual = $flavor->id;
				try{
					$mediaInfosList = $client->mediaInfo->listAction($filter);
					$flavorData->mediaInfos = $mediaInfosList;
				}
				catch(Exception $e){
					$errors[] = "Flavor [$flavor->id] flavor params outputs not found: " . $e->getMessage();
				}
				$flavorsData[] = $flavorData;
			}
		}
		$investigateData->flavorAssets = $flavorsData;
		
		$thumbsData = array();
		if(is_array($thumbs) && count($thumbs))
		{
			foreach($thumbs as $thumb)
			{
				$thumbData = new Kaltura_Client_AdminConsole_Type_InvestigateThumbAssetData();
				$thumbData->thumbAsset = $thumb;
				$thumbData->thumbParams = null;
				$thumbData->thumbParamsOutputs = array();
				$thumbData->fileSyncs = array();
				
				if(isset($thumbParams[$thumb->thumbParamsId]))
					$thumbData->thumbParams = $thumbParams[$thumb->thumbParamsId];
			
				$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
				$filter->objectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET;
				$filter->objectIdEqual = $thumb->id;
				try{
					$filesList = $fileSyncConsolePlugin->fileSync->listAction($filter);
					$thumbData->fileSyncs = $filesList;
				}
				catch(Exception $e){
					$errors[] = "Thumb [$thumb->id] files not found: " . $e->getMessage();
				}
			
				$filter = new Kaltura_Client_Type_ThumbParamsOutputFilter();
				$filter->thumbAssetIdEqual = $thumb->id;
				try{
					$thumbParamsOutputsList = $client->thumbParamsOutput->listAction($filter);
					$thumbData->thumbParamsOutputs = $thumbParamsOutputsList;
				}
				catch(Exception $e){
					$errors[] = "Thumb [$thumb->id] thumb params outputs not found: " . $e->getMessage();
				}
				$thumbsData[] = $thumbData;
			}
		}
		$investigateData->thumbAssets = $thumbsData;
		
		return $investigateData;
	}
	
	public function entryInvestigationAction()
	{
		$request = $this->getRequest();

		$this->view->errors = array();
		$this->view->investigateData = null;
		$this->view->enableActions = false;

		$action = $this->view->url(array('controller' => 'batch', 'action' => 'entry-investigation'), null, true);
				
		$this->view->searchEntryForm = new Form_Batch_SearchEntry();
		$this->view->searchEntryForm->populate($request->getParams());
        $this->view->searchEntryForm->setAction($action);
		
        $submitAction = $this->view->searchEntryForm->getElement('submitAction');
        $submitAction->setValue('');
        
        if(Infra_Support::isAdminEnabled())
        {
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
			if(count($files) && isset($files['entryFile']) && $files['entryFile']['size'])
			{
				$file = $files['entryFile'];
				$investigateData = unserialize(base64_decode(file_get_contents($file['tmp_name'])));
				
		        $entryIdField = $this->view->searchEntryForm->getElement('entryId');
		        $entryIdField->setValue($investigateData->entry->id);
		        
				$this->view->investigateData = $investigateData;
				$this->view->enableActions = false;
				
				return;
			}
        }
		
		$entryId = $request->getParam('entryId', false);
		if(!$entryId)
			return;
			
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
		
		if (($request->getParam('searchType') == 'by-flavor-asset-id'))
		{
			try
			{
				// $entryId is actually flavor id in this case
				$entry = $adminConsolePlugin->entryAdmin->getByFlavorId($entryId);
			}
			catch(Exception $e)
			{
				$this->view->errors[] = 'Flavor asset not found: ' . $e->getMessage();
				return;
			}
			$entryId = $entry->id;
		}
		
		$submitAction = $request->getParam('submitAction', false);
		if($submitAction && strlen($submitAction))
		{
			$partnerId = $request->getParam('partnerId', 0);
			Infra_ClientHelper::impersonate($partnerId);

			$errorMsg = '';
			try 
			{
				switch ($submitAction) {
					case 'retry':
						$jobId = $request->getParam('actionJobId', 0);
						$jobType = $request->getParam('actionJobType', 0);
						$errorMsg = "Retry job [$jobId] error: ";
						$client->jobs->retryJob($jobId, $jobType);
						break;
					
					case 'boostEntryJobs':
						$errorMsg = "Boost entry [$entryId] jobs error: ";
						$client->jobs->boostEntryJobs($entryId);
						break;
					
					case 'reconvertEntry':
						$errorMsg = "Reconvert entry [$entryId] error: ";
						$client->jobs->addConvertProfileJob($entryId);
						break;
					
					case 'reconvert':
						$flavorAssetId = $request->getParam('actionFlavorAssetId', 0);
						$errorMsg = "Reconvert flavor [$flavorAssetId] error: ";
						$client->flavorAsset->reconvert($flavorAssetId);
						break;
					
					case 'regenerate':
						$thumbAssetId = $request->getParam('actionFlavorAssetId', 0);
						$errorMsg = "Regenerate thumbnail [$thumbAssetId] error: ";
						$client->thumbAsset->regenerate($thumbAssetId);
						break;

					case 'restoreEntry':
						$errorMsg = "Restore entry [$entryId] error: ";
						Infra_ClientHelper::unimpersonate();
						$adminConsolePlugin = Kaltura_Client_AdminConsole_Plugin::get($client);
						$adminConsolePlugin->entryAdmin->restoreDeletedEntry($entryId);
						break;

					default:
						break;
				}
			} 
			catch (Exception $e)
			{
				$this->view->errors[] = $errorMsg . $e->getMessage();
			}
			
			Infra_ClientHelper::unimpersonate();
		}
		
		$this->view->investigateData = $this->getEntryInvestigationData($entryId, $this->view->errors);
		$this->view->enableActions = true;
		
		if(!$this->view || !$this->view->investigateData)
			return;
			
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationPartialView');
		foreach($pluginInstances as $pluginInstance)
		{
			$entryInvestigatePlugins = $pluginInstance->getApplicationPartialViews('batch', 'entryInvestigation');
			if(!$entryInvestigatePlugins)
				continue;
			
			foreach($entryInvestigatePlugins as $plugin)
			{
				/* @var $plugin Kaltura_View_Helper_PartialViewPlugin */
	    		$plugin->plug($this->view);
			}
		}
	}
	
	public function learnMoreAction()
	{
		
	}
}
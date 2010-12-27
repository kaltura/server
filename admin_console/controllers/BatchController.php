<?php
class BatchController extends Zend_Controller_Action
{

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
			
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		try{
			$entry = $client->entryAdmin->get($entryId);
		}
		catch(Exception $e){
			$this->view->errors[] = 'Entry not found: ' . $e->getMessage();
			return;
		}
		$this->view->entry = $entry;
		
		try{
			$this->view->partner = $client->systemPartner->get($entry->partnerId);
		}
		catch(Exception $e){
			$this->view->errors[] = 'Partner not found: ' . $e->getMessage();
		}
		
		$filter = new Kaltura_BatchJobFilter();
		$filter->jobTypeNotIn = KalturaBatchJobType::DELETE;
		$filter->entryIdEqual = $entryId;
		$paginatorAdapter = new Kaltura_FilterPaginator("jobs", "listBatchJobs", $filter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($this->_getParam($paginator->pageFieldName));
		$paginator->setItemCountPerPage(20);
		$paginator->setAction($action);
		
		$this->view->paginator = $paginator;
	}

	public function entryHistoryAjaxAction()
	{
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
			KalturaBatchJobStatus::ALMOST_DONE,
			KalturaBatchJobStatus::MOVEFILE,
			KalturaBatchJobStatus::PROCESSED,
			KalturaBatchJobStatus::PROCESSING,
			KalturaBatchJobStatus::QUEUED,
		);
		$inQueueStatuses = array(
			KalturaBatchJobStatus::PENDING,
			KalturaBatchJobStatus::RETRY,
		);
		$defaultJobTypes = array(
			KalturaBatchJobType::CONVERT,
			KalturaBatchJobType::IMPORT,
			KalturaBatchJobType::BULKUPLOAD,
			KalturaBatchJobType::CONVERT_PROFILE,
			KalturaBatchJobType::POSTCONVERT,
			KalturaBatchJobType::EXTRACT_MEDIA,
		);
		
		$oClass = new ReflectionClass('KalturaConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('KalturaBatchJobType');
		$jobTypes = array_flip($oClass->getConstants());
		$jobTypes[KalturaBatchJobType::CONVERT] = $convertSubTypes;
		
		$request = $this->getRequest();
		$action = $this->view->url(array('controller' => 'batch', 'action' => 'in-progress-tasks'), null, true);

		$this->view->errors = array();
		
		$this->view->tasksInProgressForm = new Form_Batch_TasksInProgress();
		$this->view->tasksInProgressForm->populate($request->getParams());
        $this->view->tasksInProgressForm->setAction($action);
		
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$abortJob = $request->getParam('abort', false);
		if ($abortJob)
		{
			Kaltura_AclHelper::validateAccess('batch', 'abort-tasks');
			$abortJobType = $request->getParam('abortType', false);
			try{
				$client->jobs->abortJob($abortJob, $abortJobType);
			}
			catch(Exception $e){
				$this->view->errors[] = 'Error aborting job: ' . $e->getMessage();
			}
		}
		
		
		$filter = new Kaltura_BatchJobFilter();
		$filter->orderBy = KalturaBaseJobOrderBy::CREATED_AT_DESC;
		$filter->jobTypeNotIn = KalturaBatchJobType::DELETE;
	
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
		$paginatorAdapter = new Kaltura_FilterPaginator("jobs", "listBatchJobs", $inProgressFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request, 'inProgressPage');
		$paginator->setCurrentPageNumber($this->_getParam($paginator->pageFieldName));
		$paginator->setItemCountPerPage($this->_getParam('pageSize', 10));
		$paginator->setAction($action);
		$this->view->inProgressPaginator = $paginator;
		
		$inQueueFilter = clone $filter;
		$inQueueFilter->statusIn = implode(',', $inQueueStatuses);
		$paginatorAdapter = new Kaltura_FilterPaginator("jobs", "listBatchJobs", $inQueueFilter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request, 'inQueuePage');
		$paginator->setCurrentPageNumber($this->_getParam($paginator->pageFieldName));
		$paginator->setItemCountPerPage($this->_getParam('pageSize', 10));
		$paginator->setAction($action);
		$this->view->inQueuePaginator = $paginator;
	}
	
	public function failedTasksAction()
	{
		$defaultJobTypes = array(
			KalturaBatchJobType::CONVERT,
			KalturaBatchJobType::IMPORT,
			KalturaBatchJobType::BULKUPLOAD,
		);
		
		$oClass = new ReflectionClass('KalturaConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('KalturaBatchJobType');
		$jobTypes = array_flip($oClass->getConstants());
		$jobTypes[KalturaBatchJobType::CONVERT] = $convertSubTypes;
		
		$oClass = new ReflectionClass('KalturaBatchJobErrorTypes');
		$errorTypes = $oClass->getConstants();
		
		$oClass = new ReflectionClass('KalturaBatchJobStatus');
		$statuses = array_flip($oClass->getConstants());
		$statuses[KalturaBatchJobStatus::FAILED] = $errorTypes;
		
		$request = $this->getRequest();
		
		$this->view->errors = array();
		
		$client = Kaltura_ClientHelper::getClient();
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
				if($submitAction == 'retry')
					$client->jobs->retryJob($jobId, $jobType);
				elseif($submitAction == 'delete')
					$client->jobs->deleteJob($jobId, $jobType);
			}
			$results = $client->doMultiRequest();
		}
		
		$action = $this->view->url(array('controller' => 'batch', 'action' => 'failed-tasks'), null, true);

		$this->view->tasksFailedForm = new Form_Batch_TasksFailed();
		
        
		$this->view->tasksFailedForm->populate($request->getParams());
        $this->view->tasksFailedForm->setAction($action);
        
        $submitAction = $this->view->tasksFailedForm->getElement('submitAction');
        $submitAction->setValue('');
		
		$filter = new Kaltura_BatchJobFilter();
		$filter->orderBy = KalturaBaseJobOrderBy::CREATED_AT_DESC;
//		$filter->jobTypeNotIn = KalturaBatchJobType::DELETE;
	
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
				KalturaBatchJobStatus::FAILED,
				KalturaBatchJobStatus::ABORTED,
				KalturaBatchJobStatus::FATAL,
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
		
		$paginatorAdapter = new Kaltura_FilterPaginator("jobs", "listBatchJobs", $filter);
		$paginator = new Kaltura_Paginator($paginatorAdapter, $request);
		$paginator->setCurrentPageNumber($this->_getParam($paginator->pageFieldName));
		$paginator->setItemCountPerPage($this->_getParam('pageSize', 10));
		$paginator->setAction($action);
		$this->view->paginator = $paginator;
	}
	
	public function setupAction()
	{
		$request = $this->getRequest();
		
		$this->view->errors = array();
		$this->view->schedulers = array();
		$this->view->workers = array();
		
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$adminId = Zend_Auth::getInstance()->getIdentity()->id;
		
		$action = $request->getParam('hdnAction', false);
		if($action)
		{
			Kaltura_AclHelper::validateAccess('batch', 'stop-start');
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
	
		$filter = new KalturaControlPanelCommandFilter();
		$filter->createdByIdEqual = $adminId;
		$filter->statusIn = KalturaControlPanelCommandStatus::HANDLED . ',' . KalturaControlPanelCommandStatus::PENDING;
		
		$this->view->disabledWorkers = array();
		try{
			$commandsList = $client->batchcontrol->listCommands($filter);
			
			foreach($commandsList->objects as $command)
				if($command->type != KalturaControlPanelCommandType::CONFIG)
					$this->view->disabledWorkers[$command->workerId] = $command;
		}
		catch(Exception $e){
			$this->view->errors[] = $e->getMessage();
		}
		
		
		$settings = Zend_Registry::get('config')->settings;
		$controlCommandsTimeFrame = $settings->controlCommandsTimeFrame * 60;
		
		$filter = new KalturaControlPanelCommandFilter();
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
			$ticketId = Kaltura_Support::addIssue($summary, $description, $content);
			if(!$ticketId)
				$errors[] = "Support ticket could not be send";
		}
	}
	
	public function getEntryInvestigationData($entryId, &$errors)
	{
		$investigateData = new KalturaInvestigateEntryData();
	
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$errors[] = 'init client failed';
			return;
		}
		
		try{
			$entry = $client->entryAdmin->get($entryId);
		}
		catch(Exception $e){
			$errors[] = 'Entry not found: ' . $e->getMessage();
			return;
		}
		$investigateData->entry = $entry;
		
		$filter = new Kaltura_BatchJobFilter();
		$filter->jobTypeNotIn = KalturaBatchJobType::DELETE;
		$filter->entryIdEqual = $entryId;
		try{
			$jobsList = $client->jobs->listBatchJobs($filter);
			$investigateData->jobs = $jobsList;
		}
		catch(Exception $e){
			$errors[] = 'Jobs not found: ' . $e->getMessage();
		}
		
		if($entry->status == KalturaEntryStatus::DELETED)
		{
			$investigateData->fileSyncs = array();
			$investigateData->flavorAssets = array();
			return $investigateData;
		}
		
		$filter = new KalturaFileSyncFilter();
		$filter->objectTypeEqual = KalturaFileSyncObjectType::ENTRY;
		$filter->objectIdEqual = $entryId;
		try{
			$filesList = $client->fileSync->listAction($filter);
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
	
		Kaltura_ClientHelper::impersonate($entry->partnerId);
		
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
	
		if(count($templateFlavorParams))
		{
			foreach($templateFlavorParams as $param)
				$flavorParams[$param->id] = $param;
		}
		if(count($partnerFlavorParams))
		{
			foreach($partnerFlavorParams as $param)
				$flavorParams[$param->id] = $param;
		}
	
		if(count($templateThumbParams))
		{
			foreach($templateThumbParams as $param)
				$thumbParams[$param->id] = $param;
		}
		if(count($partnerThumbParams))
		{
			foreach($partnerThumbParams as $param)
				$thumbParams[$param->id] = $param;
		}
		
		$flavors = null;
		try{
			$flavors = $client->flavorAsset->getByEntryId($entryId);
		}
		catch(Exception $e){
			$errors[] = 'Flavors not found: ' . $e->getMessage();
		}
		
		$thumbs = null;
		try{
			$thumbs = $client->thumbAsset->getByEntryId($entryId);
		}
		catch(Exception $e){
			$errors[] = 'Thumbs not found: ' . $e->getMessage();
		}
		
		Kaltura_ClientHelper::unimpersonate();
		
		$flavorsData = array();
		foreach($flavors as $flavor)
		{
			$flavorData = new KalturaInvestigateFlavorAssetData();
			$flavorData->flavorAsset = $flavor;
			$flavorData->flavorParams = null;
			$flavorData->flavorParamsOutputs = array();
			$flavorData->mediaInfos = array();
			$flavorData->fileSyncs = array();
			
			if(isset($flavorParams[$flavor->flavorParamsId]))
				$flavorData->flavorParams = $flavorParams[$flavor->flavorParamsId];
		
			$filter = new KalturaFileSyncFilter();
			$filter->objectTypeEqual = KalturaFileSyncObjectType::FLAVOR_ASSET;
			$filter->objectIdEqual = $flavor->id;
			try{
				$filesList = $client->fileSync->listAction($filter);
				$flavorData->fileSyncs = $filesList;
			}
			catch(Exception $e){
				$errors[] = "Flavor [$flavor->id] files not found: " . $e->getMessage();
			}
		
			$filter = new KalturaFlavorParamsOutputFilter();
			$filter->flavorAssetIdEqual = $flavor->id;
			try{
				$flavorParamsOutputsList = $client->flavorParamsOutput->listAction($filter);
				$flavorData->flavorParamsOutputs = $flavorParamsOutputsList;
			}
			catch(Exception $e){
				$errors[] = "Flavor [$flavor->id] flavor params outputs not found: " . $e->getMessage();
			}
			
			$filter = new KalturaMediaInfoFilter();
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
		$investigateData->flavorAssets = $flavorsData;
		
		$thumbsData = array();
		foreach($thumbs as $thumb)
		{
			$thumbData = new KalturaInvestigateThumbAssetData();
			$thumbData->thumbAsset = $thumb;
			$thumbData->thumbParams = null;
			$thumbData->thumbParamsOutputs = array();
			$thumbData->fileSyncs = array();
			
			if(isset($thumbParams[$thumb->thumbParamsId]))
				$thumbData->thumbParams = $thumbParams[$thumb->thumbParamsId];
		
			$filter = new KalturaFileSyncFilter();
			$filter->objectTypeEqual = KalturaFileSyncObjectType::FLAVOR_ASSET;
			$filter->objectIdEqual = $thumb->id;
			try{
				$filesList = $client->fileSync->listAction($filter);
				$thumbData->fileSyncs = $filesList;
			}
			catch(Exception $e){
				$errors[] = "Thumb [$thumb->id] files not found: " . $e->getMessage();
			}
		
			$filter = new KalturaThumbParamsOutputFilter();
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
        
        if(Kaltura_Support::isAdminEnabled())
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
			
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$this->view->errors[] = 'init client failed';
			return;
		}
		
		$submitAction = $request->getParam('submitAction', false);
		if($submitAction && strlen($submitAction))
		{
			$partnerId = $request->getParam('partnerId', 0);
			Kaltura_ClientHelper::impersonate($partnerId);
			
			if($submitAction == 'retry')
			{
				$jobId = $request->getParam('actionJobId', 0);
				$jobType = $request->getParam('actionJobType', 0);
				$client->jobs->retryJob($jobId, $jobType);
			}
			
			if($submitAction == 'reconvertEntry')
			{
				$client->jobs->addConvertProfileJob($entryId);
			}
			
			if($submitAction == 'reconvert')
			{
				$flavorAssetId = $request->getParam('actionFlavorAssetId', 0);
				$client->flavorAsset->reconvert($flavorAssetId);
			}
			
			if($submitAction == 'regenerate')
			{
				$thumbAssetId = $request->getParam('actionFlavorAssetId', 0);
				$client->thumbAsset->regenerate($thumbAssetId);
			}
			
			Kaltura_ClientHelper::unimpersonate();
		}
		
		$this->view->investigateData = $this->getEntryInvestigationData($entryId, $this->view->errors);
		$this->view->enableActions = true;
		
		$this->view->plugins = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaAdminConsoleEntryInvestigate');
		foreach($pluginInstances as $pluginInstance)
		{
			$entryInvestigatePlugins = $pluginInstance->getEntryInvestigatePlugins();
			if(!$entryInvestigatePlugins)
				continue;
			
			foreach($entryInvestigatePlugins as $plugin)
			{
	    		$this->view->addBasePath($plugin->getTemplatePath());
	    		$this->view->plugins[$plugin->getPHTML()] = $plugin->getDataArray($entryId, $partnerId);
			}
		}
	}
	
	public function learnMoreAction()
	{
		
	}
}
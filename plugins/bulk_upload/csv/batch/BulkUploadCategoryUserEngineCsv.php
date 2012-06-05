<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it. 
 * This engine class parses CSVs which describe category users.
 * 
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadCategoryUserEngineCsv extends BulkUploadEngineCsv
{
    private $categoryReferenceIdMap = array();
    
	/**
     * (non-PHPdoc)
     * @see BulkUploadGeneralEngineCsv::createUploadResult()
     */
    protected function createUploadResult($values, $columns)
	{
		if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return;
		}
		$this->handledRecordsThisRun++;
		
		$bulkUploadResult = new KalturaBulkUploadResultCategoryUser();
		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadResultObjectType::CATEGORY_USER;
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->lineIndex = $this->lineNumber;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = join(',', $values);
			 
				
		// trim the values
		array_walk($values, array('BulkUploadUserEngineCsv', 'trimArray'));
		
		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
            
			if ($column == 'categoryUserId')
			{
			    $bulkUploadResult->objectId = $values[$index];
			}
		    if ($column == 'status' && $values[$index] != KalturaCategoryUserStatus::PENDING)
			{
			    $bulkUploadResult->requiredObjectStatus = $values[$index];
			}
			
			if(iconv_strlen($values[$index], 'UTF-8'))
			{
				$bulkUploadResult->$column = $values[$index];
				KalturaLog::info("Set value $column [{$bulkUploadResult->$column}]");
			}
			else
			{
				KalturaLog::info("Value $column is empty");
			}
		}
		
		if(isset($columns['plugins']))
		{
			$bulkUploadPlugins = array();
			
			foreach($columns['plugins'] as $index => $column)
			{
				$bulkUploadPlugin = new KalturaBulkUploadPluginData();
				$bulkUploadPlugin->field = $column;
				$bulkUploadPlugin->value = iconv_strlen($values[$index], 'UTF-8') ? $values[$index] : null;
				$bulkUploadPlugins[] = $bulkUploadPlugin;
				
				KalturaLog::info("Set plugin value $column [{$bulkUploadPlugin->value}]");
			}
			
			$bulkUploadResult->pluginsData = $bulkUploadPlugins;
		}
		
		$bulkUploadResult->objectStatus = KalturaCategoryUserStatus::ACTIVE;
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		
		if (!$bulkUploadResult->action)
		{
		    $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		}
		
		$bulkUploadResult = $this->validateBulkUploadResult($bulkUploadResult);
		
		$this->bulkUploadResults[] = $bulkUploadResult;
	}
    
	protected function validateBulkUploadResult (KalturaBulkUploadResult $bulkUploadResult)
	{
	    /* @var $bulkUploadResult KalturaBulkUploadResultCategoryUser */
		if (!$bulkUploadResult->userId)
		{
		    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Missing mandatory parameter userId";
		}
		
		if (!$bulkUploadResult->categoryId && !$bulkUploadResult->categoryReferenceId)
		{
		    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Missing mandatory parameter categoryId";
		}
		
		if ($bulkUploadResult->requiredObjectStatus && !$this->isValidEnaumValue('KalturaCategoryUserStatus', $bulkUploadResult->requiredObjectStatus))
	    {
	        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property status.";
	    }
	    		
		if ($bulkUploadResult->permissionLevel && !$this->isValidEnaumValue('KalturaCategoryUserPermissionLevel', $bulkUploadResult->permissionLevel))
	    {
	        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property permissionLevel.";
	    }
	    		
		if ($bulkUploadResult->updateMethod && !$this->isValidEnaumValue('KalturaUpdateMethodType', $bulkUploadResult->updateMethod))
	    {
	        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property updateMethod.";
	    }
	    
	    
		if($this->maxRecords && $this->lineNumber > $this->maxRecords) // check max records
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Exceeded max records count per bulk";
		}
		
		if (!$bulkUploadResult->categoryId && $bulkUploadResult->categoryReferenceId)
		{
		    $filter = new KalturaCategoryFilter();
		    $filter->referenceIdEqual = $bulkUploadResult->categoryReferenceId;
		    $this->impersonate();
		    $categoryResults = $this->kClient->category->listAction($filter);
		    $this->unimpersonate();
		    
		    if ($categoryResults->objects && count($categoryResults->objects))
		    {
		        $bulkUploadResult->categoryId = $categoryResults->objects[0]->id;
		        $this->categoryReferenceIdMap[$bulkUploadResult->categoryReferenceId] = $bulkUploadResult->categoryId;
		    }
		}
        
	    if ($bulkUploadResult->action == KalturaBulkUploadAction::ADD_OR_UPDATE)
		{
		    try 
		    {
		        $categoryUser = $this->kClient->categoryUser->get($bulkUploadResult->categoryId, $bulkUploadResult->userId);
		        $bulkUploadResult->action = KalturaBulkUploadAction::UPDATE;
		    }
		    catch (Exception $e)
		    {
		        $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		    }
		}
			
		if($bulkUploadResult->status == KalturaBulkUploadResultStatus::ERROR)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return;
		}	

		
		return $bulkUploadResult;
	}
	
	
    protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		parent::addBulkUploadResult($bulkUploadResult);
		
	}
	/**
	 * 
	 * Create the entries from the given bulk upload results
	 */
	protected function createObjects()
	{
		// start a multi request for add entries
		$this->impersonate();
		$this->kClient->startMultiRequest();
		
		KalturaLog::info("job[{$this->job->id}] start creating users");
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultCategoryUser */
		    switch ($bulkUploadResult->action)
		    {
		        case KalturaBulkUploadAction::ADD:
    		        $user = $this->createCategoryUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			
        			$categoryUser = $this->kClient->categoryUser->add($user);
        			if ($bulkUploadResult->requiredObjectStatus)
        			{
        				//We push the bulk upload result in the array a second time to maintain an equal number of 
        				//multi request results and bulk upload results
        				$bulkUploadResultChunk[] = $bulkUploadResult;
        			    switch ($bulkUploadResult->requiredObjectStatus)
        			    {
        			        case KalturaCategoryUserStatus::ACTIVE:
        			            $this->kClient->categoryUser->activate($categoryUser->categoryId, $categoryUser->userId);
        			            break;
        			        case KalturaCategoryUserStatus::NOT_ACTIVE:
        			            $this->kClient->categoryUser->deactivate($categoryUser->categoryId, $categoryUser->userId);
        			            break;
        			    }
        			}
        			
		            break;
		        
		        case KalturaBulkUploadAction::UPDATE:
		            $categoryUser = $this->createCategoryUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
		    		if ($bulkUploadResult->requiredObjectStatus)
        			{
        				$bulkUploadResultChunk[] = $bulkUploadResult;
        			    switch ($bulkUploadResult->requiredObjectStatus)
        			    {
        			        case KalturaCategoryUserStatus::ACTIVE:
        			            $this->kClient->categoryUser->activate($categoryUser->categoryId, $categoryUser->userId);
        			            break;
        			        case KalturaCategoryUserStatus::NOT_ACTIVE:
        			            $this->kClient->categoryUser->deactivate($categoryUser->categoryId, $categoryUser->userId);
        			            break;
        			    }
        			}
        			
        			$this->kClient->categoryUser->update($bulkUploadResult->categoryId, $bulkUploadResult->userId, $categoryUser);
		            break;
		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->kClient->categoryUser->delete($bulkUploadResult->categoryId, $bulkUploadResult->userId);
        			
		            break;
		        
		        default:
		            $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		            $bulkUploadResult->errorDescription = "Unknown action passed: [".$bulkUploadResult->action ."]";
		            break;
		    }
		    
		    if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// handle all categoryUser objects as the partner
				$requestResults = $this->kClient->doMultiRequest();
				$this->unimpersonate();
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				$this->kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = $this->kClient->doMultiRequest();
		
		$this->unimpersonate();
		
		if(count($requestResults))
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);

		KalturaLog::info("job[{$this->job->id}] finish modifying users");
	}
	
	/**
	 * Function to create a new category user from bulk upload result.
	 * @param KalturaBulkUploadResultCategoryUser $bulkUploadCategoryUserResult
	 */
	protected function createCategoryUserFromResultAndJobData (KalturaBulkUploadResultCategoryUser $bulkUploadCategoryUserResult)
	{
	    $categoryUser = new KalturaCategoryUser();
	    //calculate parentId of the category
	    
	    if ($bulkUploadCategoryUserResult->categoryId)
	    {
	        $categoryUser->categoryId = $bulkUploadCategoryUserResult->categoryId;
	    }
	    else if ($this->categoryReferenceIdMap[$bulkUploadCategoryUserResult->categoryReferenceId])
	    {
	        $categoryUser->categoryId = $this->categoryReferenceIdMap[$bulkUploadCategoryUserResult->categoryReferenceId];
	    }
	    
	    if ($bulkUploadCategoryUserResult->userId)
	        $categoryUser->userId = $bulkUploadCategoryUserResult->userId;
	        
	    if ($bulkUploadCategoryUserResult->permissionLevel)
	        $categoryUser->permissionLevel = $bulkUploadCategoryUserResult->permissionLevel;
	        
	    $categoryUser->updateMethod = KalturaUpdateMethodType::AUTOMATIC;
	    if ($bulkUploadCategoryUserResult->updateMethod)
	        $categoryUser->updateMethod = $bulkUploadCategoryUserResult->updateMethod; 
	        
	    return $categoryUser;
	}
	
	/**
	 * 
	 * Gets the columns for CSV file
	 */
	protected function getColumns()
	{
		return array(
		    "action",
		    "categoryUserId",
		    "categoryId",
		    "categoryReferenceId",
		    "userId",
			"status",
		    "permissionLevel",
		    "updateMethod",
		);
	}
	
	
	protected function updateObjectsResults($requestResults, $bulkUploadResults)
	{
	    $this->kClient->startMultiRequest();
		KalturaLog::info("Updating " . count($requestResults) . " results");
		
		$doneWithPrev = true;
		// checking the created entries
		foreach($requestResults as $index => $requestResult)
		{
			if ($doneWithPrev)
				$bulkUploadResult = $bulkUploadResults[$index];
			
			if(is_array($requestResult) && isset($requestResult['code']))
			{
			    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->objectStatus = $requestResult['code'];
				$bulkUploadResult->errorDescription = $requestResult['message'];
				if ($bulkUploadResult->requiredObjectStatus)
				    $bulkUploadResult->requiredObjectStatus = null;
				$doneWithPrev = true;
			}
			
			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorDescription = $requestResult->getMessage();
				if ($bulkUploadResult->requiredObjectStatus)
				    $bulkUploadResult->requiredObjectStatus = null;
				$doneWithPrev = true;
			}
			
			// update the results with the new object Id
			if (property_exists(get_class($requestResult), "id") && $requestResult->id)
			    $bulkUploadResult->objectId = $requestResult->id;
			    
			if ($bulkUploadResult->requiredObjectStatus && $doneWithPrev)
			{
				$doneWithPrev = false;
			}
			else
			{
			    $doneWithPrev = true;
			}
			if ($doneWithPrev)
				$this->addBulkUploadResult($bulkUploadResult);
		}
		
		$this->kClient->doMultiRequest();
	}
}
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
		    if ($column == 'status')
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
	    if ($bulkUploadResult->action == KalturaBulkUploadAction::ADD_OR_UPDATE)
		{
		    if ( $bulkUploadResult->objectId )
		    {
		        $bulkUploadResult->action = KalturaBulkUploadAction::UPDATE;
		    }
	        else
	        {
	            $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		    }
		}
		

		if($this->lineNumber > $this->maxRecords) // check max records
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Exeeded max records count per bulk";
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
		$this->kClient->startMultiRequest();
		
		KalturaLog::info("job[{$this->job->id}] start creating users");
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultCategoryUser */
		    KalturaLog::debug("Handling bulk upload result: [". $bulkUploadResult->name ."]");
		    switch ($bulkUploadResult->action)
		    {
		        case KalturaBulkUploadAction::ADD:
    		        $user = $this->createCategoryUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$categoryUser = $this->kClient->categoryUser->add($user);
        			if ($bulkUploadResult->requiredObjectStatus)
        			{
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
        			$this->unimpersonate();
        			
		            break;
		        
		        case KalturaBulkUploadAction::UPDATE:
		            $category = $this->createCategoryUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->categoryUser->update($bulkUploadResult->objectId, $category);
        			$this->unimpersonate();
        			
        			
		            break;
		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->categoryUser->delete($bulkUploadResult->categoryId, $bulkUploadResult->userId);
        			$this->unimpersonate();
        			
		            break;
		        
		        default:
		            $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		            $bulkUploadResult->errorDescription = "Unknown action passed: [".$bulkUploadResult->action ."]";
		            break;
		    }
		    
		    if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// make all the media->add as the partner
				$requestResults = $this->kClient->doMultiRequest();
				
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				$this->kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = $this->kClient->doMultiRequest();
		
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
	        $categoryUser->categoryId = $bulkUploadCategoryUserResult->categoryId;
	    
	    if ($bulkUploadCategoryUserResult->userId)
	        $categoryUser->tags = $bulkUploadUserResult->tags;
	        
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
		
		// checking the created entries
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			
			if(is_array($requestResult) && isset($requestResult['code']))
			{
			    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->objectStatus = $requestResult['code'];
				$bulkUploadResult->errorDescription = $requestResult['message'];
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorDescription = $requestResult->getMessage();
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			// update the results with the new object Id
			if ($requestResult->id)
			    $bulkUploadResult->objectId = $requestResult->id;
			
			$this->addBulkUploadResult($bulkUploadResult);
		}
		
		$this->kClient->doMultiRequest();
	}
}
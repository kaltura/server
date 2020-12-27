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
	const OBJECT_TYPE_TITLE = 'entitlement';
	
    private $categoryReferenceIdMap = array();
    
	/**
     * (non-PHPdoc)
     * @see BulkUploadGeneralEngineCsv::createUploadResult()
     */
    protected function createUploadResult($values, $columns)
	{
	    $bulkUploadResult = parent::createUploadResult($values, $columns);
	    if (!$bulkUploadResult)
	    	return;
	    
		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::CATEGORY_USER;
				
		// trim the values
		array_walk($values, array('BulkUploadUserEngineCsv', 'trimArray'));
		
		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
            
			
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
		
		if ($bulkUploadResult->requiredObjectStatus && !$this->isValidEnumValue('KalturaCategoryUserStatus', $bulkUploadResult->requiredObjectStatus))
	    {
	        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property status.";
	    }
	    		
		if ($bulkUploadResult->permissionLevel && !$this->isValidEnumValue('KalturaCategoryUserPermissionLevel', $bulkUploadResult->permissionLevel))
	    {
	        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property permissionLevel.";
	    }
	    		
		if ($bulkUploadResult->updateMethod && !$this->isValidEnumValue('KalturaUpdateMethodType', $bulkUploadResult->updateMethod))
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
		    KBatchBase::impersonate($this->currentPartnerId);;
		    $categoryResults = KBatchBase::$kClient->category->listAction($filter);
		    KBatchBase::unimpersonate();
		    
		    if ($categoryResults->objects && count($categoryResults->objects))
		    {
		        $bulkUploadResult->categoryId = $categoryResults->objects[0]->id;
		        $this->categoryReferenceIdMap[$bulkUploadResult->categoryReferenceId] = $bulkUploadResult->categoryId;
		    }
		    else
		    {
		        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		        $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
		        $bulkUploadResult->errorDescription = "Could not locate category by given reference ID.";
		    }
		}
        
	    if ($bulkUploadResult->action == KalturaBulkUploadAction::ADD_OR_UPDATE && $bulkUploadResult->status != KalturaBulkUploadResultStatus::ERROR)
		{
		    try 
		    {
		        KBatchBase::impersonate($this->currentPartnerId);;
		        $categoryUser = KBatchBase::$kClient->categoryUser->get($bulkUploadResult->categoryId, $bulkUploadResult->userId);
                KBatchBase::unimpersonate();
		        $bulkUploadResult->action = KalturaBulkUploadAction::UPDATE;
		    }
		    catch (Exception $e)
		    {
		        $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		        KBatchBase::unimpersonate();
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
		KBatchBase::impersonate($this->currentPartnerId);;
		KBatchBase::$kClient->startMultiRequest();
		
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
        			
        			
        			$categoryUser = KBatchBase::$kClient->categoryUser->add($user);
		            break;
		        
		        case KalturaBulkUploadAction::UPDATE:
		            $categoryUser = $this->createCategoryUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			KBatchBase::$kClient->categoryUser->update($bulkUploadResult->categoryId, $bulkUploadResult->userId, $categoryUser);
		            break;
		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			$bulkUploadResult->requiredObjectStatus = null;
        			KBatchBase::$kClient->categoryUser->delete($bulkUploadResult->categoryId, $bulkUploadResult->userId);
        			
		            break;
		        
		        default:
		            $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		            $bulkUploadResult->errorDescription = "Unknown action passed: [".$bulkUploadResult->action ."]";
		            break;
		    }
		    
		    if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// handle all categoryUser objects as the partner
				$requestResults = KBatchBase::$kClient->doMultiRequest();
				KBatchBase::unimpersonate();
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::impersonate($this->currentPartnerId);;
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		
		KBatchBase::unimpersonate();
		
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
	        
	    if (!is_null($bulkUploadCategoryUserResult->permissionLevel))
	        $categoryUser->permissionLevel = $bulkUploadCategoryUserResult->permissionLevel;
	        
	    $categoryUser->updateMethod = KalturaUpdateMethodType::AUTOMATIC;
	    if (!is_null($bulkUploadCategoryUserResult->updateMethod))
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
	
	
    protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
	{
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
			
			if ($bulkUploadResult->action != KalturaBulkUploadAction::DELETE)
			    $bulkUploadResult = $this->changeCategoryKuserStatus($bulkUploadResult);
			$this->addBulkUploadResult($bulkUploadResult);
		}
		
	}
	
	protected function changeCategoryKuserStatus (KalturaBulkUploadResultCategoryUser $bulkuploadResult)
	{
	      if ($bulkuploadResult->status != KalturaBulkUploadResultStatus::ERROR)
	      {
	          KBatchBase::impersonate($this->currentPartnerId);;
	          switch ($bulkuploadResult->requiredObjectStatus)
	          {
	              case KalturaCategoryUserStatus::ACTIVE:
	                  try {
	                      KBatchBase::$kClient->categoryUser->activate($bulkuploadResult->categoryId, $bulkuploadResult->userId);
	                  }
	                  catch (Exception $e)
	                  {
	                      $bulkuploadResult->errorDescription .= $e->getMessage();
	                  }
	                  break;
	              case KalturaCategoryUserStatus::NOT_ACTIVE:
	                  try {
	                      KBatchBase::$kClient->categoryUser->deactivate($bulkuploadResult->categoryId, $bulkuploadResult->userId);
	                  }
	                  catch (Exception $e)
	                  {
	                      $bulkuploadResult->errorDescription .= $e->getMessage();
	                  }
	                  break;
	          }
	          KBatchBase::unimpersonate();
	      }
	      else
	      {
	          $bulkuploadResult->errorDescription .= 'Cannot update status - KalturaCategoryUser object was not created.';
	      }
          	      
	      return $bulkuploadResult;
	}
	
	protected function getUploadResultInstance ()
	{
	    return new KalturaBulkUploadResultCategoryUser();
	}

	protected function getUploadResultInstanceType()
	{
		return KalturaBulkUploadObjectType::CATEGORY_USER;
	}

	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
}
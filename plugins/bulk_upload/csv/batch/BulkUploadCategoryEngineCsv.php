<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it. 
 * This engine class parses CSVs which describe categories.
 * 
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadCategoryEngineCsv extends BulkUploadEngineCsv
{
    
    protected $mapFullNameToId = array();
    
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
		
		$bulkUploadResult = new KalturaBulkUploadResultCategory();
		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadResultObjectType::CATEGORY;
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->lineIndex = $this->lineNumber;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = join(',', $values);
			 
				
		// trim the values
		array_walk($values, array('BulkUploadCategoryEngineCsv', 'trimArray'));
		
		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
            
			if ($column == 'categoryId')
			{
			    $bulkUploadResult->objectId = $values[$index];
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
		
		$bulkUploadResult->objectStatus = KalturaCategoryStatus::ACTIVE;
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
		    if ( $bulkUploadResult->objectId || $bulkUploadResult->referenceId)
		    {
		        $bulkUploadResult->objectId = $this->calculateIdToUpdate($bulkUploadResult);
		        if ($bulkUploadResult->objectId)
		        {
		            $bulkUploadResult->action = KalturaBulkUploadAction::UPDATE;
		        }
		        else
		        {
		            $bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		        }
		    }
		}
		
		switch ($bulkUploadResult->action)
		{
		    case KalturaBulkUploadAction::ADD:
        		if( !$bulkUploadResult->name )
        		{
        			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
        			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
        			$bulkUploadResult->errorDescription = "Mandatory Column [name] missing from CSV.";
        		}
        			
        	    if( !$bulkUploadResult->relativePath )
        		{
        			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
        			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
        			$bulkUploadResult->errorDescription = "Mandatory Column [relativePath] missing from CSV.";
        		}
        		
		        break;
		       
		    case KalturaBulkUploadAction::UPDATE:
        		if (!$bulkUploadResult->objectId && !$bulkUploadResult->referenceId)
    		    {
    		        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
    			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
    			    $bulkUploadResult->errorDescription = "Mandatory parameters missing for action [".$bulkUploadResult->action ."]";
    		    }
                else 
                {
                    $bulkUploadResult->objectId = $this->calculateIdToUpdate($bulkUploadResult);
                }    		    
		        break;
		    
		    case KalturaBulkUploadAction::DELETE:
		        if (!$bulkUploadResult->objectId && !$bulkUploadResult->referenceId)
    		    {
    		        $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
    			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
    			    $bulkUploadResult->errorDescription = "Mandatory parameters missing for action [".$bulkUploadResult->action ."]";
    		    }
    		    else
    		    {
    		        $bulkUploadResult->objectId = $this->calculateIdToUpdate($bulkUploadResult);
    		    }
		        break;
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

		if ($bulkUploadResult->relativePath)
		{
		    $fullNameId = $this->calculateParentId($bulkUploadResult->relativePath);
		    
		    $this->mapFullNameToId[$bulkUploadResult->relativePath] = $fullNameId;
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
		
		KalturaLog::info("job[{$this->job->id}] start creating categories");
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultCategory */
		    KalturaLog::debug("Handling bulk upload result: [". $bulkUploadResult->name ."]");
		    switch ($bulkUploadResult->action)
		    {
		        case KalturaBulkUploadAction::ADD:
		            KalturaLog::debug("In handle case for action [ADD]");
    		        $category = $this->createCategoryFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->category->add($category);
        			$this->unimpersonate();
        			
		            break;
		        
		        case KalturaBulkUploadAction::UPDATE:
		            $category = $this->createCategoryFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->category->update($bulkUploadResult->objectId, $category);
        			$this->unimpersonate();
        			
        			
		            break;
		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->category->delete($bulkUploadResult->objectId);
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

		KalturaLog::info("job[{$this->job->id}] finish modifying categories");
	}
	
	/**
	 * Function to create a new category from bulk upload result.
	 * @param KalturaBulkUploadResultCategory $bulkUploadResult
	 */
	protected function createCategoryFromResultAndJobData (KalturaBulkUploadResultCategory $bulkUploadCategoryResult)
	{
	    $category = new KalturaCategory();
	    $category->name = $bulkUploadCategoryResult->name;
	    //calculate parentId of the category
	    $category->parentId = $this->mapFullNameToId[$bulkUploadCategoryResult->relativePath];
	    if ($bulkUploadCategoryResult->tags)
	        $category->tags = $bulkUploadCategoryResult->tags;
	        
	    if ($bulkUploadCategoryResult->description)
	        $category->description = $bulkUploadCategoryResult->description;
	        
	    if ($bulkUploadCategoryResult->referenceId)
	        $category->referenceId = $bulkUploadCategoryResult->referenceId; 
	           
	    if ($bulkUploadCategoryResult->contributionPolicy)
	        $category->contributionPolicy = $bulkUploadCategoryResult->contributionPolicy;

	    if ($bulkUploadCategoryResult->privacy)
	        $category->privacy = $bulkUploadCategoryResult->privacy;
	        
	    if ($bulkUploadCategoryResult->appearInList)
	        $category->appearInList = $bulkUploadCategoryResult->appearInList;
	        
	    if ($bulkUploadCategoryResult->inheritance)
	        $category->inheritance = $bulkUploadCategoryResult->inheritance;
	        
	    if ($bulkUploadCategoryResult->owner)
	        $category->owner = $bulkUploadCategoryResult->owner;
	        
	    return $category;
	}
	
	protected function calculateParentId ($fullname)
	{
	    $parentCategoryFilter = new KalturaCategoryFilter();
	    $parentCategoryFilter->fullNameEqual = $fullname;
	    $this->impersonate();
	    $parentCategoryIds = $this->kClient->category->listAction($parentCategoryFilter);
	    /* @var $parentCategoryIds KalturaCategoryListResponse*/
	    $this->unimpersonate();
	    if (!count($parentCategoryIds->objects))
	    {
	        //Error because the relative path of the new category does not exist
	    }
	    
	    if (count($parentCategoryIds->objects) > 1)
	    {
	        //Error because the relative path of the new category is not unique under the root category.
	    }
	    return $parentCategoryIds->objects[0]->id;
	}
	
	protected function calculateIdToUpdate (KalturaBulkUploadResultCategory $bulkUploadResult)
	{
	    if ($bulkUploadResult->objectId)
	    {
	        return $bulkUploadResult->objectId;
	    }
	    else if ($bulkUploadResult->referenceId)
	    {
	        $categoryFilter = new KalturaCategoryFilter();
	        $categoryFilter->referenceIdEqual = $bulkUploadResult->referenceId;
	        $this->impersonate();
	        $categoryList = $this->kClient->category->listAction($categoryFilter);
	        $this->unimpersonate();
	        if (count($categoryList->objects))
	        {
	            return $categoryList->objects[0]->id;
	        }
	    }
	}
	
	/**
	 * 
	 * Gets the columns for V1 csv file
	 */
	protected function getColumns()
	{
		return array(
		    "action",
		    "categoryId",
		    "name",
		    "relativePath",
		    "tags",
		    "description",
		    "referenceId",
		    "contributionPolicy",
		    "privacy",
		    "inheritance",
		    "owner",
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
				$bulkUploadResult->objectStatus = $requestResult['code'];
				$bulkUploadResult->errorDescription = $requestResult['message'];
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
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
<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it. 
 * This engine class parses CSVs which describe users.
 * 
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadUserEngineCsv extends BulkUploadEngineCsv
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
		
		$bulkUploadResult = new KalturaBulkUploadResultUser();
		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadResultObjectType::USER;
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
            
			if ($column == 'userId')
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
		
		$bulkUploadResult->objectStatus = KalturaUserStatus::ACTIVE;
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
		if (!$bulkUploadResult->objectId)
		{
		    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Mandatory Column [userId] missing from CSV.";
		}
		
	    if ($bulkUploadResult->action == KalturaBulkUploadAction::ADD_OR_UPDATE)
		{
		    $user = $this->kClient->user->get($bulkUploadResult->objectId);
		    if ( $user )
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
			/* @var $bulkUploadResult KalturaBulkUploadResultCategory */
		    KalturaLog::debug("Handling bulk upload result: [". $bulkUploadResult->name ."]");
		    switch ($bulkUploadResult->action)
		    {
		        case KalturaBulkUploadAction::ADD:
		            KalturaLog::debug("In handle case for action [ADD]");
    		        $user = $this->createUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->user->add($user);
        			$this->unimpersonate();
        			
		            break;
		        
		        case KalturaBulkUploadAction::UPDATE:
		            $category = $this->createUserFromResultAndJobData($bulkUploadResult);
        					
        			$bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->user->update($bulkUploadResult->objectId, $category);
        			$this->unimpersonate();
        			
        			
		            break;
		            
		        case KalturaBulkUploadAction::DELETE:
		            $bulkUploadResultChunk[] = $bulkUploadResult;
        			
        			$this->impersonate();
        			$this->kClient->user->delete($bulkUploadResult->objectId);
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
	 * Function to create a new user from bulk upload result.
	 * @param KalturaBulkUploadResultUser $bulkUploadUserResult
	 */
	protected function createUserFromResultAndJobData (KalturaBulkUploadResultUser $bulkUploadUserResult)
	{
	    $user = new KalturaUser();
	    //calculate parentId of the category
	    
	    if ($bulkUploadUserResult->objectId)
	        $user->id = $bulkUploadUserResult->objectId;
	    
	    if ($bulkUploadUserResult->tags)
	        $user->tags = $bulkUploadUserResult->tags;
	        
	    if ($bulkUploadUserResult->firstName)
	        $user->firstName = $bulkUploadUserResult->firstName;
	        
	    if ($bulkUploadUserResult->lastName)
	        $user->lastName = $bulkUploadUserResult->lastName; 
	           
	    if ($bulkUploadUserResult->email)
	        $user->email = $bulkUploadUserResult->email;

	    if ($bulkUploadUserResult->city)
	        $user->city = $bulkUploadUserResult->city;
	        
	    if ($bulkUploadUserResult->country)
	        $user->country = $bulkUploadUserResult->country;
	        
	    if ($bulkUploadUserResult->state)
	        $user->state = $bulkUploadUserResult->state;
	    
	    if ($bulkUploadUserResult->zip)
	        $user->zip = $bulkUploadUserResult->zip; 
	        
	    if ($bulkUploadUserResult->gender)
	        $user->gender = $bulkUploadUserResult->gender; 
	    
	    if ($bulkUploadUserResult->dateOfBirth)
	        $user->dateOfBirth = $bulkUploadUserResult->dateOfBirth; 
	        
	    if ($bulkUploadUserResult->isAdmin)
	        $user->isAdmin = $bulkUploadUserResult->isAdmin;
	        
	    return $user;
	}
	
	/**
	 * 
	 * Gets the columns for V1 csv file
	 */
	protected function getColumns()
	{
		return array(
		    "action",
		    "userId",
		    "firstName",
		    "lastName",
		    "email",
		    "isAdmin",
		    "tags",
		    "gender",
		    "zip",
		    "country",
		    "state",
			"city",
		    "dateOfBirth",
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
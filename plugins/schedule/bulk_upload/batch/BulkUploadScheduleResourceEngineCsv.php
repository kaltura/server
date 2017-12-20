<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it. 
 * This engine class parses CSVs which describe schedule-resource objects.
 * 
 * @package plugins.scheduleBulkUpload
 * @subpackage batch
 */
class BulkUploadScheduleResourceEngineCsv extends BulkUploadEngineCsv
{
	const OBJECT_TYPE_TITLE = 'schedule-resource';
    const MAX_IN_FILTER = 100;
	
    protected static $validTypes = array(
		'location',
    	'camera',
    	'live_entry',
    );
    
    protected $existingSystemNames = array();
    protected $parentSystemNames = array();
    
	/**
	 * (non-PHPdoc)
	 * 
	 * @see BulkUploadGeneralEngineCsv::createUploadResult()
	 */
	protected function createUploadResult($values, $columns)
	{
		$bulkUploadResult = parent::createUploadResult($values, $columns);
		if(!$bulkUploadResult)
			return;
		
		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::SCHEDULE_RESOURCE;
		
		// trim the values
		array_walk($values, array('BulkUploadScheduleResourceEngineCsv', 'trimArray'));
		
		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
			
			if(iconv_strlen($values[$index], 'UTF-8'))
			{
				$bulkUploadResult->$column = $values[$index];
			}
		}
		
		$bulkUploadResult->objectStatus = KalturaScheduleResourceStatus::ACTIVE;
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		
		if(!$bulkUploadResult->action)
		{
			$bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		}
		
		$bulkUploadResult = $this->validateBulkUploadResult($bulkUploadResult);
		if($bulkUploadResult)
			$this->bulkUploadResults[] = $bulkUploadResult;
	}
	
	protected function validateBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		/* @var $bulkUploadResult KalturaBulkUploadResultScheduleResource */
		if(!$bulkUploadResult->resourceId && !$bulkUploadResult->systemName && ($bulkUploadResult->action == KalturaBulkUploadAction::UPDATE || $bulkUploadResult->action == KalturaBulkUploadAction::DELETE))
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Mandatory Columns [resourceId or systemName] missing from CSV.";
		}
		
		if($bulkUploadResult->type && !in_array($bulkUploadResult->type, self::$validTypes))
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property type [$bulkUploadResult->type]";
		}
		if(!$bulkUploadResult->type)
		{
			$bulkUploadResult->type = 'location';
		}
		
		if($bulkUploadResult->parentType && !in_array($bulkUploadResult->parentType, self::$validTypes))
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Wrong value passed for property parentType [$bulkUploadResult->parentType]";
		}
		if(!$bulkUploadResult->parentType)
		{
			$bulkUploadResult->parentType = 'location';
		}
		
		if($this->maxRecords && $this->lineNumber > $this->maxRecords) // check max records
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Exeeded max records count per bulk";
		}
		
		if($bulkUploadResult->status == KalturaBulkUploadResultStatus::ERROR)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return null;
		}
		
		return $bulkUploadResult;
	}
	
	/**
	 * Retrieve resources by system-names and replace the system-name with id
	 */
	protected function validateSystemNames($type, $filterSystemNames)
	{
		$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		
		KBatchBase::impersonate($this->currentPartnerId);
		switch($type)
		{
			case 'location':
				$filter = new KalturaLocationScheduleResourceFilter();
				break;

			case 'camera':
				$filter = new KalturaCameraScheduleResourceFilter();
				break;

			case 'live_entry':
				$filter = new KalturaLiveEntryScheduleResourceFilter();
				break;
						
		}
		$filter->systemNameIn = implode(',', $filterSystemNames);
		$response = $schedulePlugin->scheduleResource->listAction($filter);
		foreach($response->objects as $scheduleResource)
		{
			if(!isset($this->existingSystemNames[$type]))
				$this->existingSystemNames[$type] = array();
			
			$this->existingSystemNames[$type][$scheduleResource->systemName] = $scheduleResource->id;
		}
		KBatchBase::unimpersonate();
	}
	
	/**
	 * Create the entries from the given bulk upload results
	 */
	protected function createObjects()
	{
		$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		
		$filterSystemNames = array();
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			if($bulkUploadResult->systemName && !$bulkUploadResult->resourceId && $bulkUploadResult->action != KalturaBulkUploadAction::ADD)
			{
				if(!isset($filterSystemNames[$bulkUploadResult->type]))
					$filterSystemNames[$bulkUploadResult->type] = array();
				
				$filterSystemNames[$bulkUploadResult->type][] = $bulkUploadResult->systemName;
				if(count($filterSystemNames[$bulkUploadResult->type] >= self::MAX_IN_FILTER))
				{
					$this->validateSystemNames($bulkUploadResult->type, $filterSystemNames[$bulkUploadResult->type]);
					$filterSystemNames[$bulkUploadResult->type] = array();
				}
			}
			
			if($bulkUploadResult->parentSystemName)
			{
				if(!isset($filterSystemNames[$bulkUploadResult->parentType]))
					$filterSystemNames[$bulkUploadResult->parentType] = array();
				
				$filterSystemNames[$bulkUploadResult->parentType][] = $bulkUploadResult->parentSystemName;
				if(count($filterSystemNames[$bulkUploadResult->parentType] >= self::MAX_IN_FILTER))
				{
					$this->validateSystemNames($bulkUploadResult->parentType, $filterSystemNames[$bulkUploadResult->parentType]);
					$filterSystemNames[$bulkUploadResult->parentType] = array();
				}
			}
		}
		foreach($filterSystemNames as $type => $names)
		{
			if(count($names))
				$this->validateSystemNames($type, $names);
		}
		
		// start a multi request for add entries
		KBatchBase::$kClient->startMultiRequest();
		
		KalturaLog::info("job[{$this->job->id}] start creating resources");
		$bulkUploadResultChunk = array(); // store the results of the created entries
		
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultScheduleResource */
			KalturaLog::info("Handling bulk upload result: [" . ($bulkUploadResult->resourceId ? $bulkUploadResult->resourceId : $bulkUploadResult->systemName) . "]");

			if(!$bulkUploadResult->resourceId && $bulkUploadResult->systemName && isset($this->existingSystemNames[$bulkUploadResult->type]))
			{
				$existingSystemNames = $this->existingSystemNames[$bulkUploadResult->type];
				if(isset($existingSystemNames[$bulkUploadResult->systemName]))
				{
					$bulkUploadResult->resourceId = $existingSystemNames[$bulkUploadResult->systemName];
				}
			}
			
			if($bulkUploadResult->action == KalturaBulkUploadAction::ADD_OR_UPDATE)
			{
				$bulkUploadResult->action = $bulkUploadResult->resourceId ? KalturaBulkUploadAction::UPDATE : KalturaBulkUploadAction::ADD;
			}
			
			
			KBatchBase::impersonate($this->currentPartnerId);
			switch($bulkUploadResult->action)
			{
				case KalturaBulkUploadAction::ADD:
					$scheduleResource = $this->createScheduleResourceFromResultAndJobData($bulkUploadResult);
					$bulkUploadResultChunk[] = $bulkUploadResult;
					if($scheduleResource)
					{
						$createdResource = $schedulePlugin->scheduleResource->add($scheduleResource);
						if($bulkUploadResult->systemName)
						{
							if(!isset($this->parentSystemNames[$bulkUploadResult->type]))
								$this->parentSystemNames[$bulkUploadResult->type] = array();
							
							$this->parentSystemNames[$bulkUploadResult->type][$bulkUploadResult->systemName] = "{$createdResource->id}";
						}
					}
					else 
					{
						KBatchBase::$kClient->system->ping(); // just to increment the multi-request index
					}
					break;
				
				case KalturaBulkUploadAction::UPDATE:
				
					$scheduleResource = null;
					if(!$bulkUploadResult->resourceId)
					{
						$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
						$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
						$bulkUploadResult->errorDescription = "Unable to find {$bulkUploadResult->type} resource [$bulkUploadResult->systemName]";
					}
					else
					{
						$scheduleResource = $this->createScheduleResourceFromResultAndJobData($bulkUploadResult);
					}
					$bulkUploadResultChunk[] = $bulkUploadResult;
					if($scheduleResource)
					{
						$schedulePlugin->scheduleResource->update($bulkUploadResult->resourceId, $scheduleResource);
						if($bulkUploadResult->resourceId && $bulkUploadResult->systemName)
						{
							if(!isset($this->existingSystemNames[$bulkUploadResult->type]))
								$this->existingSystemNames[$bulkUploadResult->type] = array();
							
							$this->existingSystemNames[$bulkUploadResult->type][$bulkUploadResult->systemName] = $bulkUploadResult->resourceId;
						}
					}
					else 
					{
						KBatchBase::$kClient->system->ping(); // just to increment the multi-request index
					}
					break;
				
				case KalturaBulkUploadAction::DELETE:
					if(!$bulkUploadResult->resourceId)
					{
						$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
						$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
						$bulkUploadResult->errorDescription = "Unable to find {$bulkUploadResult->type} resource [$bulkUploadResult->systemName]";
					}
					$bulkUploadResultChunk[] = $bulkUploadResult;
					if($bulkUploadResult->resourceId)
					{
						$schedulePlugin->scheduleResource->delete($bulkUploadResult->resourceId);
					}
					else 
					{
						KBatchBase::$kClient->system->ping(); // just to increment the multi-request index
					}
					break;
				
				default:
					$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
					$bulkUploadResult->errorDescription = "Unknown action passed: [" . $bulkUploadResult->action . "]";
					break;
			}
			KBatchBase::unimpersonate();
			
			if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// make all the media->add as the partner
				$requestResults = KBatchBase::$kClient->doMultiRequest();
				
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}
		
		// make all the category actions as the partner
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		
		if(count($requestResults))
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
		
		KalturaLog::info("job[{$this->job->id}] finish modifying resources");
	}
	
	/**
	 * Function to create a new schedule-resource from bulk upload result.
	 * 
	 * @param KalturaBulkUploadResultScheduleResource $bulkUploadResult   
	 * @return KalturaScheduleResource
	 */
	protected function createScheduleResourceFromResultAndJobData(KalturaBulkUploadResultScheduleResource &$bulkUploadResult)
	{
		switch($bulkUploadResult->type)
		{
			case 'location':
				$scheduleResource = new KalturaLocationScheduleResource();
				break;

			case 'camera':
				$scheduleResource = new KalturaCameraScheduleResource();
				break;

			case 'live_entry':
				$scheduleResource = new KalturaLiveEntryScheduleResource();
				break;
						
		}
		
		if($bulkUploadResult->name)
			$scheduleResource->name = $bulkUploadResult->name;
		
		if($bulkUploadResult->systemName)
			$scheduleResource->systemName = $bulkUploadResult->systemName;
		
		if($bulkUploadResult->description)
			$scheduleResource->description = $bulkUploadResult->description;
		
		if($bulkUploadResult->tags)
			$scheduleResource->tags = $bulkUploadResult->tags;

		if($bulkUploadResult->entryId)
			$scheduleResource->entryId = $bulkUploadResult->entryId;

		if($bulkUploadResult->streamUrl)
			$scheduleResource->streamUrl = $bulkUploadResult->streamUrl;

		if($bulkUploadResult->parentSystemName)
		{
			if(isset($this->existingSystemNames[$bulkUploadResult->parentType]) && isset($this->existingSystemNames[$bulkUploadResult->parentType][$bulkUploadResult->parentSystemName]))
			{
				$scheduleResource->parentId = $this->existingSystemNames[$bulkUploadResult->parentType][$bulkUploadResult->parentSystemName];
			}
			elseif(isset($this->parentSystemNames[$bulkUploadResult->parentType]) && isset($this->parentSystemNames[$bulkUploadResult->parentType][$bulkUploadResult->parentSystemName]))
			{
				$scheduleResource->parentId = $this->parentSystemNames[$bulkUploadResult->parentType][$bulkUploadResult->parentSystemName];
			}
			else
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
				$bulkUploadResult->errorDescription = "Unable to find parent {$bulkUploadResult->parentType} resource [$bulkUploadResult->parentSystemName]";
				return null;
			}
		}
			
		return $scheduleResource;
	}
	
	/**
	 * {@inheritDoc}
	 * @see BulkUploadEngineCsv::getColumns()
	 */
	protected function getColumns()
	{
		return array(
			'action', 
			'resourceId', 
			'name',
			'type',
			'systemName',
			'description',
			'tags',
			'parentType',
			'parentSystemName',
			'entryId',
			'streamUrl',
		);
	}
	
	/**
	 * {@inheritDoc}
	 * @see KBulkUploadEngine::updateObjectsResults()
	 */
	protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
	{
		KBatchBase::$kClient->startMultiRequest();
		
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
			
			if($requestResult instanceof KalturaScheduleResource)
			{
				if ($requestResult->id)
				    $bulkUploadResult->objectId = $requestResult->id;

				if($requestResult->systemName && isset($this->parentSystemNames[$bulkUploadResult->type]) && isset($this->parentSystemNames[$bulkUploadResult->type][$requestResult->systemName]))
				{
					if(!isset($this->existingSystemNames[$bulkUploadResult->type]))
						$this->existingSystemNames[$bulkUploadResult->type] = array();
					
					$this->existingSystemNames[$bulkUploadResult->type][$requestResult->systemName] = $requestResult->id;
					unset($this->parentSystemNames[$bulkUploadResult->type][$requestResult->systemName]);
				}
			}
			
			$this->addBulkUploadResult($bulkUploadResult);
		}
		
		KBatchBase::$kClient->doMultiRequest();
	}
	
	/**
	 * {@inheritDoc}
	 * @see BulkUploadEngineCsv::getUploadResultInstance()
	 */
	protected function getUploadResultInstance()
	{
		return new KalturaBulkUploadResultScheduleResource();
	}
	
	/**
	 * {@inheritDoc}
	 * @see KBulkUploadEngine::getObjectTypeTitle()
	 */
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
}
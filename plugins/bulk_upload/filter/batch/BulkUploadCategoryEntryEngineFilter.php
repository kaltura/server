<?php
/**
 * This engine supports create / delete of category entries based on the input filter
 * 
 * @package plugins.bulkUploadFilter
 * @subpackage batch
 */
class BulkUploadCategoryEntryEngineFilter extends BulkUploadEngineFilter
{
    const OBJECT_TYPE_TITLE = 'category entry';
    
	/**
	 * Function to create a new category from bulk upload result.
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 */
	protected function createObjectFromResultAndJobData (KalturaBulkUploadResult $bulkUploadResult)
	{
	    $categoryEntry = new KalturaCategoryEntry();
	    
	    if ($bulkUploadResult->entryId)
	        $categoryEntry->entryId = $bulkUploadResult->entryId;
	        
	    if ($bulkUploadResult->categoryId)
	        $categoryEntry->categoryId = $bulkUploadResult->categoryId;
        
	    if ($this->getData()->templateObject->entryId)
	        $categoryEntry->entryId = $this->getData()->templateObject->entryId;
	    
	    if ($this->getData()->templateObject->categoryId)
	        $categoryEntry->categoryId = $this->getData()->templateObject->categoryId;
        
	    return KBatchBase::$kClient->categoryEntry->add($categoryEntry);
	}

	protected function deleteObjectFromResult (KalturaBulkUploadResult $bulkUploadResult)
	{
		return KBatchBase::$kClient->categoryEntry->delete($bulkUploadResult->entryId, $bulkUploadResult->categoryId);
	}
	
	/**
	 * create specific instance ob BulkUploadResult and set it's properties
	 * @param $object - Result can be created either from KalturaBaseEntry or from KalturaCategoryEntry depending on the 
	 * filter passed to the job
	 * 
	 * @see BulkUploadEngineFilter::fillUploadResultInstance()
	 */
	protected function fillUploadResultInstance ($object)
	{
	    $bulkUploadResult = new KalturaBulkUploadResultCategoryEntry();
	    if($object instanceof KalturaBaseEntry)
	    {
	    	//get category entry object based on the entry details
	    	$filter = new KalturaCategoryEntryFilter();
	    	$filter->entryIdEqual = $object->id;
	    	$filter->categoryIdEqual = $object->categoryId;
	    	$list = $this->listObjects($filter);
	    	if(count($list->objects))
	    	{
	    		$categoryEntry = reset($list->objects);
	    	}	    	
	    }
	    else if($object instanceof KalturaCategoryEntry)
	    {
	    	$categoryEntry = $object;
	    }
	    if($categoryEntry)
	    {
	    	$bulkUploadResult->objectId = $categoryEntry->categoryId.':'.$categoryEntry->entryId;
			$bulkUploadResult->objectStatus = $categoryEntry->status;
			$bulkUploadResult->entryId = $categoryEntry->entryId;
			$bulkUploadResult->categoryId = $categoryEntry->categoryId;		
	    	
	    }
	    return $bulkUploadResult;
	}
	
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
	
	/* get a list of objects according to the input filter
	 * Can either filter entries by if entry filter is passed or category entries if category entry filter is passed
	 * 
	 * @see BulkUploadEngineFilter::listObjects()
	 */
	protected function listObjects(KalturaFilter $filter, KalturaFilterPager $pager = null) 
	{
		KBatchBase::impersonate($this->currentPartnerId);
		
		$filter->orderBy = "+createdAt";
		
		if($filter instanceof KalturaBaseEntryFilter)
			return KBatchBase::$kClient->baseEntry->listAction($filter, $pager);
		else if($filter instanceof KalturaCategoryEntryFilter)
		{
			$filter->statusEqual = KalturaCategoryEntryStatus::ACTIVE;
			return KBatchBase::$kClient->categoryEntry->listAction($filter, $pager);	
		}
		else	
			throw new KalturaBatchException("Unsupported filter: {get_class($filter)}", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED); 			
			
		KBatchBase::unimpersonate();	
	}

	protected function getBulkUploadResultObjectType()
	{
		return KalturaBulkUploadObjectType::CATEGORY_ENTRY;
	}
	
	protected function isErrorResult($requestResult){
		if(is_array($requestResult) && isset($requestResult['code'])){
			if($requestResult['code'] == 'CATEGORY_ENTRY_ALREADY_EXISTS')
				return false;
			else 
				return true;
		}
		if($requestResult instanceof Exception){
			if($requestResult->getCode() == 'CATEGORY_ENTRY_ALREADY_EXISTS')
				return false;
			else
				return true;
		}
		return false;
	}
}
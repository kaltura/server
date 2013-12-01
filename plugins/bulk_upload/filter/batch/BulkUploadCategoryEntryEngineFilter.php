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

	    KalturaLog::debug("template category id:". $this->getData()->templateObject->categoryId);
	    
	    if ($this->getData()->templateObject->categoryId)
	        $categoryEntry->categoryId = $this->getData()->templateObject->categoryId;
        
	    return KBatchBase::$kClient->categoryEntry->add($categoryEntry);
	}

	protected function deleteObjectFromResult (KalturaBulkUploadResult $bulkUploadResult)
	{
		return KBatchBase::$kClient->categoryEntry->delete($bulkUploadResult->entryId, $bulkUploadResult->categoryId);
	}
	
	protected function fillUploadResultInstance ($object)
	{
	    $bulkUploadResult = new KalturaBulkUploadResultCategoryEntry();
	    if($object instanceof KalturaBaseEntry)
	    {
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
	
	/* (non-PHPdoc)
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
		return KalturaBulkUploadResultObjectType::CATEGORY_ENTRY;
	}
}
<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */
class KCopyingCategoryEntryEngine extends KCopyingEngine
{
	/* (non-PHPdoc)
	 * @see KCopyingEngine::copy()
	 */
	protected function copy(KalturaFilter $filter, KalturaObjectBase $templateObject) {
		return $this->copyCategoryEntries ($filter, $templateObject);
		
	}

	protected function copyCategoryEntries (KalturaFilter $filter, KalturaObjectBase $templateObject)
	{
		/* @var $filter KalturaCategoryEntryFilter */
		$filter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		
		$categoryEntryList = KBatchBase::$kClient->categoryEntry->listAction($filter, $this->pager);
		if(!count($categoryEntryList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryEntryList->objects as $categoryEntry)
		{
			$newCategoryEntry = $this->getNewObject($categoryEntry, $templateObject);
			KBatchBase::$kClient->categoryEntry->add($newCategoryEntry);
		}
		
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);
				
		if(!count($results))
			return 0;
			
		$lastCopyId = end($results);
		$this->setLastCopyId($lastCopyId);
		
		return count($results);
	}
	/* (non-PHPdoc)
	 * @see KCopyingEngine::getNewObject()
	 */
	protected function getNewObject(KalturaObjectBase $sourceObject, KalturaObjectBase $templateObject) {
		$class = get_class($sourceObject);
		$newObject = new $class();
		
		/* @var $newObject KalturaCategoryEntry */
		/* @var $sourceObject KalturaCategoryEntry */
		/* @var $templateObject KalturaCategoryEntry */
		
		$newObject->categoryId = $sourceObject->categoryId;
		$newObject->entryId = $sourceObject->entryId;
			
		if(!is_null($templateObject->categoryId))
			$newObject->categoryId = $templateObject->categoryId;
		if(!is_null($templateObject->entryId))
			$newObject->entryId = $templateObject->entryId;
	
		return $newObject;
	}	
}
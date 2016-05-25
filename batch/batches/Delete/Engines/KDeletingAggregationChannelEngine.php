<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingAggregationChannelEngine extends  KDeletingEngine
{
	protected $lastCreatedAt;
	
	protected $publicAggregationChannel;
	protected $excludedCategories;
	
	public function configure($partnerId, $jobData)
	{
		/* @var $jobData KalturaDeleteJobData */
		parent::configure($partnerId, $jobData);

		$this->publicAggregationChannel = $jobData->filter->aggregationCategoriesMultiLikeAnd;
		$this->excludedCategories = $this->retrievePublishingCategories ($jobData->filter);
	}
	
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter) {
		return $this->deleteAggregationCategoryEntries ($filter);
		
	}
	
	protected function deleteAggregationCategoryEntries (KalturaCategoryFilter $filter)
	{
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->categoriesIdsNotContains = $this->excludedCategories;
		$entryFilter->categoriesIdsMatchAnd = $this->publicAggregationChannel . "," . $filter->idNotIn;
		
		$entryFilter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		if ($this->lastCreatedAt)
		{
			$entryFilter->createdAtGreaterThanOrEqual = $this->lastCreatedAt;
		}
		
		$entryFilter->statusIn = implode (',', array (KalturaEntryStatus::ERROR_CONVERTING, KalturaEntryStatus::ERROR_IMPORTING, KalturaEntryStatus::IMPORT, KalturaEntryStatus::NO_CONTENT, KalturaEntryStatus::READY));
		$entriesList = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $this->pager);
		if(!count($entriesList->objects))
			return 0;
			
		$this->lastCreatedAt = $entriesList->objects[count ($entriesList->objects) -1];
		KBatchBase::$kClient->startMultiRequest();
		foreach($entriesList->objects as $entry)
		{
			/* @var $entry KalturaBaseEntry */
			KBatchBase::$kClient->categoryEntry->delete($entry->id, $this->publicAggregationChannel);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);

		return count($results);
		
	}
	
	protected function retrievePublishingCategories (KalturaCategoryFilter $filter)
	{
		$categoryPager = new KalturaFilterPager();
		$categoryPager->pageIndex = 1;
		$categoryPager->pageSize = 500;
		
		$categoryIdsToReturn = array ();
		
		$categoryResponse = KBatchBase::$kClient->category->listAction($filter, $categoryPager);
		while (count ($categoryResponse->objects))
		{
			foreach ($categoryResponse->objects as $category)
			{
				$categoryIdsToReturn[] = $category->id;
			}
			
			$categoryPager->pageIndex++;
			$categoryResponse = KBatchBase::$kClient->category->listAction($filter, $categoryPager);
		}
		
		return implode (',', $categoryIdsToReturn);
	}

	
}
<?php
/**
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */

/**
 * Will move category entries from source category to destination category
 *
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */
class KAsyncMoveCategoryEntries extends KJobHandlerWorker
{
	const CATEGORY_ENTRY_ALREADY_EXISTS = 'CATEGORY_ENTRY_ALREADY_EXISTS';
	const INVALID_ENTRY_ID = 'INVALID_ENTRY_ID';
	const CATEGORY_NOT_FOUND = 'CATEGORY_NOT_FOUND';
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::MOVE_CATEGORY_ENTRIES;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getPrivileges()
	 */
	protected function getPrivileges()
	{
		return array_merge(parent::getPrivileges(), array(self::PRIVILEGE_BATCH_JOB_TYPE . ':' . self::getType()));
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->move($job, $job->data);
	}
	
	/**
	 * Moves category entries from source category to destination category
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMoveCategoryEntriesJobData $data
	 * 
	 * @return KalturaBatchJob
	 */
	protected function move(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data)
	{
	    KBatchBase::impersonate($job->partnerId);
		
		$job = $this->moveCategory($job, $data);
		KBatchBase::unimpersonate();
		$job = $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}
	
	/**
	 * Go through all categories tree and call moveEntries
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaMoveCategoryEntriesJobData $data
	 * @param int $srcCategoryId Current source category id
	 * 
	 * @return KalturaBatchJob
	 */
	private function moveCategory(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data, $srcCategoryId = null)
	{
	    
		if(is_null($srcCategoryId))
			$srcCategoryId = $data->srcCategoryId;

		$movedEntries = $this->moveEntries($job, $data, $srcCategoryId);

		KBatchBase::unimpersonate();
		$this->updateJob($job, "Moved [$movedEntries] entries", KalturaBatchJobStatus::PROCESSING, $data);
		KBatchBase::impersonate($job->partnerId);
		
		return $job;
	}
	
	private function addCategoryEntries($categoryEntriesList, $destCategoryId, &$entryIds, &$categoryIds)
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryEntriesList->objects as $oldCategoryEntry)
		{
			/* @var $categoryEntry KalturaCategoryEntry */
			$newCategoryEntry = new KalturaCategoryEntry();
			$newCategoryEntry->entryId = $oldCategoryEntry->entryId;
			$newCategoryEntry->categoryId = $destCategoryId;
			KBatchBase::$kClient->categoryEntry->add($newCategoryEntry);
			$entryIds[] = $oldCategoryEntry->entryId;
			$categoryIds[] = $oldCategoryEntry->categoryId;
		}
		return KBatchBase::$kClient->doMultiRequest();
	}
	
	/**
	 * Moves category entries from source category to destination category
	 */
	private function moveEntries(KalturaBatchJob $job, KalturaMoveCategoryEntriesJobData $data, $srcCategoryId)
	{
		$categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->orderBy = KalturaCategoryEntryOrderBy::CREATED_AT_ASC;
		if($data->moveFromChildren)
			$categoryEntryFilter->categoryFullIdsStartsWith = $data->destCategoryFullIds;
		else
			$categoryEntryFilter->categoryIdEqual = $srcCategoryId;

		$categoryEntryPager = new KalturaFilterPager();
		$categoryEntryPager->pageSize = 100;
		$categoryEntryPager->pageIndex = 1;

		if(KBatchBase::$taskConfig->params->pageSize)
			$categoryEntryPager->pageSize = KBatchBase::$taskConfig->params->pageSize;
			
		$movedEntries = 0;
		$categoryEntriesList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		do {
			$entryIds = array();
			$categoryIds = array();

			$addedCategoryEntriesResults = $this->addCategoryEntries($categoryEntriesList, $data->destCategoryId, $entryIds, $categoryIds);

			KBatchBase::$kClient->startMultiRequest();
			foreach($addedCategoryEntriesResults as $index => $addedCategoryEntryResult)
			{
				$code = null;
				if(KBatchBase::$kClient->isError($addedCategoryEntryResult))
				{
					$code = $addedCategoryEntryResult['code'];
					if (!in_array($code, array(self::CATEGORY_ENTRY_ALREADY_EXISTS, self::INVALID_ENTRY_ID)))
					{
						throw new KalturaException($addedCategoryEntryResult['message'], $addedCategoryEntryResult['code'], $addedCategoryEntryResult['args']);
					}
				}
				KBatchBase::$kClient->categoryEntry->delete($entryIds[$index], $categoryIds[$index]);
			}

			$deletedCategoryEntriesResults = KBatchBase::$kClient->doMultiRequest();
			if(is_null($deletedCategoryEntriesResults))
				$deletedCategoryEntriesResults = array();

			foreach($deletedCategoryEntriesResults as $index => $deletedCategoryEntryResult)
			{
				if(is_array($deletedCategoryEntryResult) && isset($deletedCategoryEntryResult['code']))
				{
					KalturaLog::err('error: ' . $deletedCategoryEntryResult['code']);
					unset($deletedCategoryEntriesResults[$index]);
				}
			}

			$movedEntries += count($deletedCategoryEntriesResults);
			$categoryEntriesList = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $categoryEntryPager);
		} while(count($categoryEntriesList->objects) == $categoryEntryPager->pageSize);

		KBatchBase::$kClient->category->index($data->destCategoryId);
		
		return $movedEntries;
	}
}

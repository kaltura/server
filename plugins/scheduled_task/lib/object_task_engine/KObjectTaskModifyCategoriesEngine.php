<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskModifyCategoriesEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaModifyCategoriesObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$entryId = $object->id;
		$addRemoveType = $objectTask->addRemoveType;
		$taskCategoryIds = array();
		if (!is_array($objectTask->categoryIds))
			$objectTask->categoryIds = array();
		foreach($objectTask->categoryIds as $categoryIntValue)
		{
			/** @var KalturaString $categoryIntValue */
			$taskCategoryIds[] = $categoryIntValue->value;
		}

		if ($addRemoveType == KalturaScheduledTaskAddOrRemoveType::MOVE)
		{
			$this->removeAllCategories($entryId, $object->partnerId);
			$addRemoveType = KalturaScheduledTaskAddOrRemoveType::ADD;
		}

		// remove all categories if nothing was configured in the list
		if (count($taskCategoryIds) == 0 && $addRemoveType == KalturaScheduledTaskAddOrRemoveType::REMOVE)
		{
			$this->removeAllCategories($entryId, $object->partnerId);
		}
		else
		{
			foreach($taskCategoryIds as $categoryId)
			{
				try
				{
					$this->impersonate($object->partnerId);
					$this->processCategory($entryId, $categoryId, $addRemoveType);
					$this->unimpersonate();
				}
				catch(Exception $ex)
				{
					$this->unimpersonate();
					KalturaLog::err($ex);
				}
			}
		}
	}

	/**
	 * @param $entryId
	 * @param $categoryId
	 * @param $addRemoveType
	 */
	public function processCategory($entryId, $categoryId, $addRemoveType)
	{
		$client = $this->getClient();
		$categoryEntry = null;
		$filter = new KalturaCategoryEntryFilter();
		$filter->entryIdEqual = $entryId;
		$filter->categoryIdEqual = $categoryId;
		$categoryEntryListResponse = $client->categoryEntry->listAction($filter);
		/** @var KalturaCategoryEntry $categoryEntry */
		if (count($categoryEntryListResponse->objects))
			$categoryEntry = $categoryEntryListResponse->objects[0];

		if (is_null($categoryEntry) && $addRemoveType == KalturaScheduledTaskAddOrRemoveType::ADD)
		{
			$categoryEntry = new KalturaCategoryEntry();
			$categoryEntry->entryId = $entryId;
			$categoryEntry->categoryId = $categoryId;
			$client->categoryEntry->add($categoryEntry);
		}
		elseif (!is_null($categoryEntry) && $addRemoveType == KalturaScheduledTaskAddOrRemoveType::REMOVE)
		{
			$client->categoryEntry->delete($entryId, $categoryId);
		}
	}

	/**
	 * @param $entryId
	 * @param $partnerId
	 */
	public function removeAllCategories($entryId, $partnerId)
	{
		try
		{
			$this->impersonate($partnerId);
			$this->doRemoveAllCategories($entryId);
			$this->unimpersonate();
		}
		catch(Exception $ex)
		{
			$this->unimpersonate();
			KalturaLog::err($ex);
		}
	}

	/**
	 * @param $entryId
	 */
	public function doRemoveAllCategories($entryId)
	{
		$client = $this->getClient();
		$filter = new KalturaCategoryEntryFilter();
		$filter->entryIdEqual = $entryId;
		$categoryEntryListResponse = $client->categoryEntry->listAction($filter);
		foreach($categoryEntryListResponse->objects as $categoryEntry)
		{
			/** @var $categoryEntry KalturaCategoryEntry */
			$client->categoryEntry->delete($entryId, $categoryEntry->categoryId);
		}
	}
}
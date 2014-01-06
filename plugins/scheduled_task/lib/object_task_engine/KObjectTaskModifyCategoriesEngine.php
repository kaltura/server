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

		$taskCategoryIds = array();
		foreach($objectTask->categoryIds as $categoryIntValue)
		{
			/** @var KalturaString $categoryIntValue */
			$taskCategoryIds[] = $categoryIntValue->value;
		}

		foreach($taskCategoryIds as $categoryId)
		{
			$entryId = $object->id;
			$addRemoveType = $objectTask->addRemoveType;

			try
			{
				$this->impersonate($object->partnerId);
				$this->processCategory($entryId, $categoryId, $addRemoveType);
				$this->unimpersonate();
			}
			catch(Exception $ex)
			{
				KalturaLog::err($ex);
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
}
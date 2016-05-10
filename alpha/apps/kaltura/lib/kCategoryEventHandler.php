<?php
class kCategoryEventHandler implements kObjectDeletedEventConsumer, kObjectCreatedEventConsumer, kObjectChangedEventConsumer
{
	
	const PUBLIC_AGGREGATION_CATEGORY = 'publicAggregationCategory';
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) {
		if ($object instanceof categoryEntry)
		{
			$this->handleCategoryEntryCreated($object);
		}
		
		if ($object instanceof category)
		{
			$this->handleCategoryChanged($object, $modifiedColumns);
		}
		
		return true;
	}
	
	protected function handleCategoryChanged (category $object, array $modifiedColumns)
	{
		$oldCustomDataValues = $object->getCustomDataOldValues();
		$oldAggregationCategories = isset ($oldCustomDataValues[''][category::AGGREGATION_CATEGORIES]) ? explode (',', $oldCustomDataValues[''][category::AGGREGATION_CATEGORIES]): array();
		$currentAggregationCategories = explode (',', $object->getAggregationCategories());
		
		$aggregationCategoriesToAdd = array_diff($currentAggregationCategories, $oldAggregationCategories);
		KalturaLog::info ("Copying entries from category ID [" . $object->getId() . "] to aggregation channels: " . print_r($aggregationCategoriesToAdd, true));
		$this->addToAggregationCategories($object, $aggregationCategoriesToAdd);
		
		$aggregationCategoriesToRemove = array_diff ($oldAggregationCategories, $currentAggregationCategories);
		
		KalturaLog::info ("Removing entries from category ID [" . $object->getId() . "] to aggregation channels: " . print_r($aggregationCategoriesToRemove, true));
		$this->deleteFromAggregationChannels ($object, $aggregationCategoriesToRemove);
		
	}
	
	protected function deleteFromAggregationChannels (category $object, array $aggregationCatIds)
	{
		$aggregationCategories = categoryPeer::retrieveByPKs($aggregationCatIds);
		foreach ($aggregationCategories as $aggregationCategory)
		{
			/* @var $aggregationCategory category */
			$currentPublishingCategories = explode (',', $aggregationCategory->getPublishingCategories());
			$currentPublishingCategories = array_diff($currentPublishingCategories, array ($object->getId()));
			$aggregationCategory->setPublishingCategories(implode(',', $currentPublishingCategories));
			$aggregationCategory->save();
			
			$this->addDeleteAggregationCategoryEntryJob ($object, $aggregationCategory);
		}
	}
	
	protected function addDeleteAggregationCategoryEntryJob (category $object, category $aggregationCategory)
	{
		$filter = new entryFilter();
		$filter->set("_matchand_categories_ids", $object->getId().','.$aggregationCategory->getId());
		$filter->set ("_notcontains_categories_ids", $aggregationCategory->getPublishingCategories());
		
		$additionalParameters = array();
		$pair[self::PUBLIC_AGGREGATION_CATEGORY] = $aggregationCategory->getId();
		$additionalParameters[] = $pair;
		kJobsManager::addDeleteJob($object->getPartnerId(), DeleteObjectType::CATEGORY_ENTRY_AGGREGATION, $filter, $additionalParameters);
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) {
		if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == CategoryEntryStatus::ACTIVE
			&& $object->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
		{
			return true;
		}
		
		if ($object instanceof category)
		{
			$oldCustomDataValues = $object->getCustomDataOldValues();
			$oldAggregationChannels = isset ($oldCustomDataValues[''][category::AGGREGATION_CATEGORIES]) ? $oldCustomDataValues[''][category::AGGREGATION_CATEGORIES] : '';
			
			if ($oldAggregationChannels != $object->getAggregationCategories())
			{
				return true;
			}
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object) {
		if ($object instanceof categoryEntry)
		{
			$this->handleCategoryEntryCreated($object);
		}
		
		if ($object instanceof category)
		{
			$this->handleCategoryCreated ($object);
		}
		
		return true;
		
	}
	
	protected function handleCategoryCreated (category $object)
	{
		if (!$object->getAggregationCategories())
		{
			KalturaLog::info ("Category [" . $object->getId() . "] has no aggregation channels" );
			return true;
		}
		
		$this->addToAggregationCategories($object, explode (',', $object->getAggregationCategories()));
	}
	
	protected function addCopyJobToAggregationChannel (category $object, $aggregationCategoryId)
	{
		$templateObject = new categoryEntry();
		$templateObject->setCategoryId($aggregationCategoryId);
		
		$filter = new categoryEntryFilter();
		$filter->set("_eq_category_id", $object->getId());
		kJobsManager::addCopyJob($object->getPartnerId(), CopyObjectType::CATEGORY_ENTRY, $filter, $templateObject);
	}
	
	protected function addToAggregationCategories (category $object, array $aggregationCatIds)
	{
		$aggregationCategories = categoryPeer::retrieveByPKs($aggregationCatIds);
		foreach ($aggregationCategories as $aggregationCategory)
		{
			/* @var $aggregationCategory category */
			$currentPublishingCategories = explode (',', $aggregationCategory->getPublishingCategories());
			$currentPublishingCategories[] = $object->getId();
			$currentPublishingCategories = array_unique($currentPublishingCategories);
			$aggregationCategory->setPublishingCategories(implode(',', $currentPublishingCategories));
			$aggregationCategory->save();
			
			$this->addCopyJobToAggregationChannel ($object, $aggregationCategory->getId());
		}
	}
	
	protected function handleCategoryEntryCreated (categoryEntry $object)
	{
			$category = categoryPeer::retrieveByPK($object->getCategoryId());
			if (!$category)
			{
				KalturaLog::info("category [" . $object->getCategoryId() . "] does not exist in the system.");
				return true;
			}
			
			if (!$category->getAggregationCategories())
			{
				KalturaLog::info("No aggregation categories found for category [" . $category->getId() . "]");
				return true;
			}
			
			$aggregationCategories = explode(',', $category->getAggregationCategories());
			$aggregationCategoryEntries = categoryEntryPeer::retrieveActiveByEntryIdAndCategoryIds($object->getEntryId(), $aggregationCategories); 
			
			foreach ($aggregationCategoryEntries as $aggregationCategoryEntry)
			{
				$aggregationCategories = array_diff($aggregationCategories, array($aggregationCategoryEntry->getCategoryId()));
			}
			
			foreach ($aggregationCategories as $categoryIdToAdd)
			{
				$aggregationCategory = categoryPeer::retrieveByPK($categoryIdToAdd);
				if (!$aggregationCategory)
					continue;
				
				$categoryEntry = $object->copy();
				$categoryEntry->setCategoryId($categoryIdToAdd);
				$categoryEntry->setCategoryFullIds($aggregationCategory->getFullIds());
				$categoryEntry->save();
			}
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object) {
		if ($object instanceof categoryEntry && $object->getStatus() == CategoryEntryStatus::ACTIVE)
		{
			return true;
		}
		
		if ($object instanceof category && $object->getAggregationCategories())
		{
			return true;
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) {
		if ($object instanceof categoryEntry)
		{
			$this->handleCategoryEntryDeleted ($object);
		}
		
		if ($object instanceof category)
		{
			$this->handleCategoryDeleted($object);
		}
		
		return true;
	}
	
	protected function handleCategoryDeleted (category $object)
	{
		if (!$object->getAggregationCategories())
		{
			KalturaLog::info ("Category [" . $object->getId() . "] has no aggregation channels" );
			return true;
		}
		
		$this->deleteFromAggregationChannels($object, explode (',', $object->getAggregationCategories()));
	}
	
	protected function handleCategoryEntryDeleted (categoryEntry $object)
	{
		$category = categoryPeer::retrieveByPK($object->getCategoryId());
		if (!$category)
		{
			KalturaLog::info("category [" . $object->getCategoryId() . "] does not exist in the system.");
			return true;
		}
		
		if (!$category->getAggregationCategories())
		{
			KalturaLog::info("No aggregation categories found for category [" . $category->getId() . "]");
			return true;
		}
		//If this categoryEntry was deleted because its category was deleted, this will be handled later on.
		if ($category->getStatus() == CategoryStatus::DELETED || $category->getStatus() == CategoryStatus::PURGED)
		{
			KalturaLog::info("Category ID [" . $category->getId() . "] is deleted, its deleted entries will be handled separately");
			return true;
		}
		
		$aggregationCategories = explode (',', $category->getAggregationCategories());
		
		//List all entry's ACTIVE categoryEntry objects
		$activeCategoryEntries = categoryEntryPeer::retrieveActiveByEntryId($object->getEntryId());
		$activeCategoryIds = array();
		foreach ($activeCategoryEntries as $activeCategoryEntry)
		{
			/* @var $activeCategoryEntry categoryEntry */
			$activeCategoryIds[] = $activeCategoryEntry->getCategoryId();
		}
		
		$activeCategories = categoryPeer::retrieveByPKs($activeCategoryIds);
		foreach ($activeCategories as $activeCat)
		{
			/* @var $activeCat category */
			$activeCatAggregationCats = explode(',', $activeCat->getAggregationCategories());
			$aggregationCategories = array_diff($aggregationCategories, $activeCatAggregationCats);
			
			if (!count ($aggregationCategories))
			{
				KalturaLog::info("No need to delete any aggregation category associations.");
				return true;
			}
		}
		
		if (count ($aggregationCategories))
		{
			$aggregationCategoryEntries = categoryEntryPeer::retrieveActiveByEntryIdAndCategoryIds($object->getEntryId(), $aggregationCategories);
			foreach ($aggregationCategoryEntries as $aggregationCategoryEntry)
			{
				/* @var $aggregationCategoryEntry categoryEntry */
				KalturaLog::info("Delete aggregation category entry- entry ID [" . $aggregationCategoryEntry->getEntryId() . "], category ID [" . $aggregationCategoryEntry->getCategoryId() . "]");
				$aggregationCategoryEntry->setStatus(CategoryEntryStatus::DELETED);
				$aggregationCategoryEntry->save();
			}
		}
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object) {
		if ($object instanceof categoryEntry && $object->getStatus() == CategoryEntryStatus::DELETED)
		{
			return true;
		}
		
		if ($object instanceof category && $object->getStatus() == CategoryStatus::DELETED)
		{
			return true;
		}
		
		return false;
	}

	
}
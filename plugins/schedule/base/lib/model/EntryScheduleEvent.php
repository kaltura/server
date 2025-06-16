<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class EntryScheduleEvent extends ScheduleEvent
{
	const CUSTOM_DATA_FIELD_TEMPLATE_ENTRY_ID = 'template_entry_id';
	const CUSTOM_DATA_FIELD_ENTRY_IDS = 'entry_ids';
	const CUSTOM_DATA_FIELD_CATEGORY_IDS = 'category_ids';

	const CATEGORY_PARENT_SEARCH_PERFIX = 'p';
	const CATEGORIES_INDEXED_FIELD_PREFIX = 'pid';

	/**
	 * @param string $v
	 */
	public function setTemplateEntryId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_TEMPLATE_ENTRY_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getTemplateEntryId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_TEMPLATE_ENTRY_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setEntryIds($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ENTRY_IDS, $v);
	}
	
	/**
	 * @return string
	 */
	public function getEntryIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ENTRY_IDS);
	}

	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_TEMPLATE_ENTRY_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setCategoryIds($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_CATEGORY_IDS, $v);
	}
	
	/**
	 * @return string
	 */
	public function getCategoryIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CATEGORY_IDS);
	}
	
	public function getCategoryIdsForIndex()
	{
		$allCategories = !is_null($this->getCategoryIds()) ?
			categoryPeer::retrieveByPKs(explode(',', $this->getCategoryIds())) :
			array();
		
		$index = array();
		foreach($allCategories as $category)
		{
			/* @var $category category */
			
			$index[] = $category->getId();
			
			//index all category's parents - for easier searchs on entry->list with filter of categoriesMatchOr
			$categoryFullIds = explode(categoryPeer::CATEGORY_SEPARATOR, $category->getFullIds());
			foreach($categoryFullIds as $categoryId)
			{
				if(trim($categoryId) && $categoryId != $category->getId())
				{
					$index[] = self::CATEGORY_PARENT_SEARCH_PERFIX . $categoryId;
				}
			}
		}
		
		$index = array_unique($index);
		
		return self::CATEGORIES_INDEXED_FIELD_PREFIX . $this->getPartnerId() . " " .  implode(' ', $index);
	}

	public function getTemplateEntryCategoriesIdsForIndex()
	{
		$templateEntryId = $this->getTemplateEntryId();
		if ( !isset($templateEntryId))
			return '';

		$categoryEntries = categoryEntryPeer::retrieveActiveByEntryId($templateEntryId);
		$catgoriesIds = array();
		foreach($categoryEntries as $categoryEntry)
		{
			/* @var $categoryEntry CategoryEntry */
			$categoryId = $categoryEntry->getCategoryId();
			$catgoriesIds[] = $categoryId;
		}
		return implode(' ', $catgoriesIds);
	}

	public function getBlackoutConflicts()
	{
		if($this->getRecurrenceType() === ScheduleEventRecurrenceType::RECURRING)
		{
			$duration = $this->getDuration();
		}
		else
		{
			$duration = $this->getEndDate(null) - $this->getStartDate(null);
		}

		$events = ScheduleEventPeer::retrieveBlackoutEventsByDateWindow($this->getStartDate(null),$this->getStartDate(null)
			+ $duration);

		return $events;
	}
	
	/**
	 * @param $context - binding string from the caller to the action
	 * @param $output - the new output value
	 * @return bool - stop processing true / false
	 */
	public function dynamicGetter($context, &$output)
	{
		return false;
	}
}

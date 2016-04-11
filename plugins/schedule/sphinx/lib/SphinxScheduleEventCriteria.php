<?php

class SphinxScheduleEventCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "ScheduleEventIndex";
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "schedule_event.$fieldName";
		}
		
		$scheduleEventFields = ScheduleEventPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $scheduleEventFields);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter ScheduleEventFilter */
		
		if($filter->get('_free_text'))
		{
			$this->sphinxSkipped = false;
			$freeTexts = $filter->get('_free_text');
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, ScheduleEventFilter::FREE_TEXT_FIELDS);
		}
		$filter->unsetByName('_free_text');

		if($filter->get('_like_parent_category_ids'))
		{
			$ids = explode(',', $filter->get('_like_parent_category_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = EntryScheduleEvent::CATEGORY_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_like_parent_category_ids');
			$filter->set('_like_category_ids', implode(',', $ids));
		}

		if($filter->get('_mlikeor_parent_category_ids'))
		{
			$ids = explode(',', $filter->get('_mlikeor_parent_category_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = EntryScheduleEvent::CATEGORY_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_mlikeor_parent_category_ids');
			$filter->set('_mlikeor_category_ids', implode(',', $ids));
		}

		if($filter->get('_mlikeand_parent_category_ids'))
		{
			$ids = explode(',', $filter->get('_mlikeand_parent_category_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = EntryScheduleEvent::CATEGORY_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_mlikeand_parent_category_ids');
			$filter->set('_mlikeand_category_ids', implode(',', $ids));
		}

		if($filter->get('_like_parent_resource_ids'))
		{
			$ids = explode(',', $filter->get('_like_parent_resource_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = ScheduleEvent::RESOURCE_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_like_parent_resource_ids');
			$filter->set('_like_resource_ids', implode(',', $ids));
		}

		if($filter->get('_mlikeor_parent_resource_ids'))
		{
			$ids = explode(',', $filter->get('_mlikeor_parent_resource_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = ScheduleEvent::RESOURCE_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_mlikeor_parent_resource_ids');
			$filter->set('_mlikeor_resource_ids', implode(',', $ids));
		}

		if($filter->get('_mlikeand_parent_resource_ids'))
		{
			$ids = explode(',', $filter->get('_mlikeand_parent_resource_ids'));
			foreach($ids as $index => $id)
				$ids[$index] = ScheduleEvent::RESOURCE_PARENT_SEARCH_PERFIX . $id;

			$filter->unsetByName('_mlikeand_parent_resource_ids');
			$filter->set('_mlikeand_resource_ids', implode(',', $ids));
		}
		
		return parent::applyFilterFields($filter);
	}
}
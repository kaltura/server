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

		if($filter->is_set('_like_resource_system_names'))
		{

			$systemNames = explode(',', $filter->get('_like_resource_system_names'));
			$systemNamesMd5 = array();
			foreach($systemNames as $systemName){
				if( $systemNames!=null && $systemNames!='' )
					$systemNamesMd5[] = $this->buildMd5String($systemName);
			}
			$filter->set('_like_resource_system_names', implode(',', $systemNamesMd5));
		}

		if($filter->is_set('_mlikeor_resource_system_names'))
		{

			$systemNames = explode(',', $filter->get('_mlikeor_resource_system_names'));
			$systemNamesMd5 = array();
			foreach($systemNames as $systemName){
				if( $systemNames!=null && $systemNames!='' )
					$systemNamesMd5[] = $this->buildMd5String($systemName);
			}
			$filter->set('_mlikeor_resource_system_names', implode(',', $systemNamesMd5));
		}

		if($filter->is_set('_mlikeand_resource_system_names'))
		{
			$systemNames = explode(',', $filter->get('_mlikeand_resource_system_names'));
			$systemNamesMd5 = array();
			foreach($systemNames as $systemName){
				if( $systemNames!=null && $systemNames!='' )
					$systemNamesMd5[] = $this->buildMd5String($systemName);
			}
			$filter->set('_mlikeand_resource_system_names', implode(',', $systemNamesMd5));
		}

		if($filter->is_set('_eq_resource_ids'))
		{
			$resourceId = ScheduleEvent::RESOURCES_INDEXED_FIELD_PREFIX . kCurrentContext::getCurrentPartnerId() . " " .  $filter->get('_eq_resource_ids');
			$filter->unsetByName('_eq_resource_ids');
			$filter->set('_eq_resource_ids', $resourceId);
		}

		return parent::applyFilterFields($filter);
	}

	public function getTranslateIndexId($id)
	{
		return $id;
	}

	private function buildMd5String($str)
	{
		return mySearchUtils::getMd5EncodedString($str);
	}
}
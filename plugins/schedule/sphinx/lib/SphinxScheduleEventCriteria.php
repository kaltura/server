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

		return parent::applyFilterFields($filter);
	}
}
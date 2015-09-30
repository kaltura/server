<?php

class SphinxAnswerCuePointCriteria extends SphinxCuePointCriteria
{
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter AnswerCuePointFilter */
		if($filter->get('_eq_quiz_user_entry_id'))
		{
		    $userEntryId = $filter->get('_eq_quiz_user_entry_id');
		    $searchValue = $userEntryId . kCurrentContext::getCurrentPartnerId() . QuizPlugin::SEARCH_TEXT_SUFFIX;
		    $this->addMatch("(@plugins_data $searchValue)");
		}
		$filter->unsetByName('_eq_quiz_user_entry_id');
		
		if($filter->get('_in_quiz_user_entry_id'))
		{
		    $userEntryIds = explode(',', $filter->get('_in_quiz_user_entry_id'));
		    foreach($userEntryIds as &$userEntryId)
		    {
		      $userEntryId .= kCurrentContext::getCurrentPartnerId() . QuizPlugin::SEARCH_TEXT_SUFFIX;
		    }
		    $searchValues = implode(' | ', $userEntryIds);
		    $this->addMatch("(@plugins_data ($searchValues))");
		}
		$filter->unsetByName('_in_quiz_user_entry_id');		

		return parent::applyFilterFields($filter);
	}
}
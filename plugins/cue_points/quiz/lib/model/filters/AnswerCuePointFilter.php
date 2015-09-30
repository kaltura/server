<?php
/**
 * @package plugins.quiz
 * @subpackage model.filters
 */
class AnswerCuePointFilter extends CuePointFilter
{
	public function init ()
	{
	    parent::init();
	    
	    $extendedFields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_quiz_user_entry_id",
				"_in_quiz_user_entry_id",
			) , NULL );
	    
		$this->fields = array_merge($this->fields , $extendedFields);
	}
}

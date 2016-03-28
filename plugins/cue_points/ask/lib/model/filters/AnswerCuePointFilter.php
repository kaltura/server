<?php
/**
 * @package plugins.ask
 * @subpackage model.filters
 */
class AnswerCuePointFilter extends CuePointFilter
{
	public function init ()
	{
	    parent::init();
	    
	    $extendedFields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_ask_user_entry_id",
				"_in_ask_user_entry_id",
			) , NULL );
	    
		$this->fields = array_merge($this->fields , $extendedFields);
	}
}

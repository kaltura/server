<?php
/**
 * @package plugins.sessionCuePoint
 * @subpackage model.filters
 */
class SessionCuePointFilter extends CuePointFilter
{
	public function init ()
	{
	    parent::init();
	    
	    $extendedFields = kArray::makeAssociativeDefaultValue ( array (
				"_gte_end_time",
				"_lte_end_time",
				"_gte_duration",
				"_lte_duration",
			) , NULL );
	    
		$this->fields = array_merge($this->fields , $extendedFields);
	}
}

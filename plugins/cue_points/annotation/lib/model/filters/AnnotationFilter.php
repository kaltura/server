<?php
/**
 * @package plugins.annotation
 * @subpackage model.filters
 */
class AnnotationFilter extends CuePointFilter
{
	public function init ()
	{
	    parent::init();
	    
	    $extendedFields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_parent_id",
				"_in_parent_id",
				"_like_text",
				"_mlikeor_text",
				"_mlikeand_text",
				"_gte_end_time",
				"_lte_end_time",
				"_gte_duration",
				"_lte_duration",
				"_eq_is_public",
			) , NULL );
	    
		$this->fields = array_merge($this->fields , $extendedFields);
	}
}

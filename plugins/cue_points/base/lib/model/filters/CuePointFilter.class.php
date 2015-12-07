<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.filters
 */
class CuePointFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'name,tags,text';
	
	// allow only 256 charaters when creation a MATCH-AGAINST caluse
	const MAX_SAERCH_TEXT_SIZE = 256;
	
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_id",
				"_in_id",
				"_eq_type",
				"_in_type",
				"_eq_sub_type",
				"_in_sub_type",
				"_eq_status",
				"_in_status",
				"_eq_entry_id",
				"_in_entry_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_like_name",
				"_mlikeor_name",
				"_mlikeand_name",
				"_eq_name",
				"_in_name",
				"_like_text",
				"_mlikeor_text",
				"_mlikeand_text",
				"_like_tags",
				"_mlikeor_tags",
				"_mlikeand_tags",
				"_gte_start_time",
				"_lte_start_time",
				"_eq_user_id",
				"_in_user_id",
				"_eq_partner_sort_value",
				"_in_partner_sort_value",
				"_gte_partner_sort_value",
				"_lte_partner_sort_value",
				"_eq_force_stop",
				"_in_force_stop",
				"_eq_system_name",
				"_in_system_name",
				"_gte_end_time",
				"_lte_end_time",
				"_gte_duration",
				"_lte_duration",
				"_eq_parent_id",
				"_in_parent_id",
				"_eq_cue_point_type", 
				"_in_cue_point_type",
				"_free_text",
			) , NULL );

		$this->allowed_order_fields = array (
			"created_at", 
			"updated_at", 
			"start_time", 
			"end_time", 
			"duration", 
			"partner_sort_value",
		);
			
		$this->aliases = array ( 
			"user_id" => "kuser_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "CuePoint",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		if($field_name == 'force_stop')
			return CuePointPeer::FORCE_STOP;
			
		if($field_name == 'duration')
			return CuePointPeer::DURATION;
			
		return CuePointPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer (  )
	{
		return CuePointPeer::ID;
	}
}


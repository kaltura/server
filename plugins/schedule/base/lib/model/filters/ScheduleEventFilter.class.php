<?php
/**
 * @package plugins.schedule
 * @subpackage model.filters
 */
class ScheduleEventFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_id",
				"_in_id",
				"_notin_id",
				"_eq_parent_id",
				"_in_parent_id",
				"_notin_parent_id",
				"_eq_status",
				"_in_status",
				"_gte_start_date",
				"_lte_start_date",
				"_gte_end_data",
				"_lte_end_data",
				"_eq_reference_id",
				"_in_reference_id",
				"_eq_organizer_user_id",
				"_in_organizer_user_id",
				"_eq_priority",
				"_in_priority",
				"_gte_priority",
				"_lte_priority",
				"_eq_recurance_type",
				"_in_recurance_type",
				"_matchand_categories_ids",
				"_matchor_categories_ids",
				"_matchand_resources_ids",
				"_matchor_resources_ids",
				"_empty_resources_ids",
				"_like_tags",
				"_mlikeor_tags",
				"_mlikeand_tags",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
			) , NULL );

		$this->allowed_order_fields = array (
			"created_at", 
			"updated_at", 
			"start_date", 
			"end_data", 
			"priority", 
		);
			
		$this->aliases = array ( 
			"organizer_user_id" => "organizer_kuser_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ScheduleEvent",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer($field_name)
	{
		return ScheduleEventPeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	
	public function getIdFromPeer()
	{
		return ScheduleEventPeer::ID;
	}
}


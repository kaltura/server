<?php
/**
 * @package plugins.schedule
 * @subpackage model.filters
 */
class ScheduleEventFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'summary,description,tags,reference_id,location,contact,comment';
	
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
				"_gte_end_date",
				"_lte_end_date",
				"_eq_reference_id",
				"_in_reference_id",
				"_eq_owner_id",
				"_in_owner_id",
				"_eq_priority",
				"_in_priority",
				"_gte_priority",
				"_lte_priority",
				"_eq_recurrence_type",
				"_in_recurrence_type",
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
				"_like_entry_ids",
				"_mlikeor_entry_ids",
				"_mlikeand_entry_ids",
				"_like_category_ids",
				"_mlikeor_category_ids",
				"_mlikeand_category_ids",
				"_like_resource_ids",
				"_mlikeor_resource_ids",
				"_mlikeand_resource_ids",
				"_like_parent_category_ids",
				"_mlikeor_parent_category_ids",
				"_mlikeand_parent_category_ids",
				"_like_parent_resource_ids",
				"_mlikeor_parent_resource_ids",
				"_mlikeand_parent_resource_ids",
				"_mlikeor_template_entry_categories_ids",
				"_mlikeand_template_entry_categories_ids",
				"_like_template_entry_categories_ids",
				"_mlikeor_resource_system_names",
				"_mlikeand_resource_system_names",
				"_like_resource_system_names",
				"_eq_template_entry_id",
				"_eq_resource_ids",
			) , NULL );

		$this->allowed_order_fields = array (
			"created_at", 
			"updated_at", 
			"start_date", 
			"end_date", 
			"priority",
			"summary",
		);
			
		$this->aliases = array ( 
			"owner_id" => "owner_kuser_id",
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

	public function setTemplateEntryIdEqual($v)
	{
		$this->set('_eq_template_entry_id', ($v));
	}

	public function setResourceIdsIn($ids)
	{
		$this->set('_mlikeor_resource_ids', $ids);
	}
}


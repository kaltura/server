<?php
/**
 * @package plugins.schedule
 * @subpackage model.filters
 */
class ScheduleResourceFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_id",
				"_in_id",
				"_notin_id",
				"_eq_parent_id",
				"_in_parent_id",
				"_eq_status",
				"_in_status",
				"_like_tags",
				"_mlikeor_tags",
				"_mlikeand_tags",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_system_name",
				"_in_system_name",
				"_eq_name",
			) , NULL );

		$this->allowed_order_fields = array (
			"created_at", 
			"updated_at", 
		);
			
		$this->aliases = array ( 
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ScheduleResource",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer($field_name)
	{
		return ScheduleResourcePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	
	public function getIdFromPeer()
	{
		return ScheduleResourcePeer::ID;
	}
}


<?php
/**
 * @package plugins.reach
 * @subpackage model.filters
 */
class ReachProfileFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_status",
			"_in_status",
			"_eq_profile_type",
			"_in_profile_type",
		) , NULL );
		
		$this->allowed_order_fields = array (
			"id",
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
				"display_name" => "ReachProfile",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer($field_name)
	{
		return ReachProfilePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	
	public function getIdFromPeer()
	{
		return ReachProfilePeer::ID;
	}
}


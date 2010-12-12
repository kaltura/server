<?php
class EntryDistributionFilter extends baseObjectFilter
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
			"_gte_submitted_at",
			"_lte_submitted_at",
			"_eq_entry_id",
			"_in_entry_id",
			"_eq_distribution_profile_id",
			"_in_distribution_profile_id",
			"_eq_status",
			"_in_status",
			"_eq_dirty_status",
			"_in_dirty_status",
			"_gte_sunrise",
			"_lte_sunrise",
			"_gte_sunset",
			"_lte_sunset",
			) , null );

		$this->allowed_order_fields = array ("created_at" , "updated_at");
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "EntryDistributionFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = EntryDistributionPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return EntryDistributionPeer::ID;
	}
}

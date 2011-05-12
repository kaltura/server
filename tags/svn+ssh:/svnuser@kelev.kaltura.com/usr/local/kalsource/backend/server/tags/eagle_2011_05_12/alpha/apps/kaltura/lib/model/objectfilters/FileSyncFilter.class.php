<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class FileSyncFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_partner_id",
			"_eq_object_type",
			"_in_object_type",
			"_eq_object_id",
			"_in_object_id",
			"_eq_version",
			"_in_version",
			"_eq_object_sub_type",
			"_in_object_sub_type",
			"_eq_dc",
			"_in_dc",
			"_eq_original",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_ready_at",
			"_lte_ready_at",
			"_gte_sync_time",
			"_lte_sync_time",
			"_eq_status",
			"_in_status",
			"_eq_file_type",
			"_in_file_type",
			"_eq_linked_id",
			"_gte_link_count",
			"_lte_link_count",
			"_gte_file_size",
			"_lte_file_size",
			) , NULL );
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "FileSyncFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = FileSyncPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return FileSyncPeer::ID;
	}
}

?>
<?php
/**
 * @package plugins.metadata
 * @subpackage model.filters
 */ 
class MetadataFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_partner_id",
			"_eq_metadata_profile_id",
			"_eq_metadata_profile_version",
			"_gte_metadata_profile_version",
			"_lte_metadata_profile_version",
			"_eq_object_type",
			"_eq_object_id",
			"_in_object_id",
			"_eq_version",
			"_gte_version",
			"_lte_version",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_status",
			"_in_status",
			"_eq_metadata_object_type"
			) , NULL );

		$this->allowed_order_fields = array ("created_at" , "updated_at", "metadata_profile_version" , "version");
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "MetadataFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = MetadataPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return MetadataPeer::ID;
	}
}

?>
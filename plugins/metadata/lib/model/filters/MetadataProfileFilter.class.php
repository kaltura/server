<?php

class MetadataProfileFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id" , 
			"_eq_partner_id",
			"_eq_object_type",
			"_eq_version",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_status",
			"_in_status",
			) , NULL );

		$this->allowed_order_fields = array ("created_at" , "updated_at")	;
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "MetadataProfileFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = MetadataProfilePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return MetadataProfilePeer::ID;
	}
}

?>
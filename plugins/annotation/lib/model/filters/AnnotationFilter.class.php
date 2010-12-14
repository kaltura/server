<?php

class AnnotationFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
		//TODO - add filters
				"_eq_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_entry_id",
				"_in_entry_id",
				"_eq_parent_id",
				"_in_parent_id",
				"_eq_user_id",
				"_in_user_id",
			) , NULL );

		$this->allowed_order_fields = array ("created_at" , "updated_at");
			
		$this->aliases = array ( 
			"user_id" => "kuser_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "Annotation",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = AnnotationPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return AnnotationPeer::ID;
	}
}

?>
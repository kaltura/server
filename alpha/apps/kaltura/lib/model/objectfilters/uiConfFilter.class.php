<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class uiConfFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id" , 
			"_in_id",
			"_gte_id" ,
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_status" ,
			"_eq_obj_type" ,
			"_in_obj_type" ,
			"_like_name" ,
			"_mlikeor_tags" ,
			"_mlikeand_tags" ,
			"_gte_created_at" ,
			"_lte_created_at" ,
			"_gte_updated_at" ,
			"_lte_updated_at" ,
			"_in_display_in_search" ,
			"_eq_creation_mode",
			"_in_creation_mode" ,
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "status" , "obj_type")	;
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "UiConfFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = uiConfPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return uiConfPeer::ID;
	}
}

?>
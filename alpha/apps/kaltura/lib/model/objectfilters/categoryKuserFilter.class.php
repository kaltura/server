<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class categoryKuserFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_category_id",
				"_in_category_id",
				"_eq_user_id",
				"_in_user_id",
				"_eq_parent_id",
				"_in_parent_id",
				"_eq_depth",
				"_eq_full_name",
				"_likex_full_name",
				"_like_tags",
				"_mlikeor_tags",
				"_mlikeand_tags",
				"_eq_appear_in_list",
				"_eq_privacy",
				"_in_privacy",
				"_eq_membership_setting",
				"_in_membership_setting",
				"_eq_status",
				"_in_status",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "full_name");
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "CategoryKuserFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = categoryKuserPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return categoryKuserPeer::ID;
	}
}

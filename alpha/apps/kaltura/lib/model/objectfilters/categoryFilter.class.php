<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class categoryFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'name,tags,description';
	
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_parent_id",
			"_in_parent_id",
			"_eq_full_name",
			"_likex_full_name",
			"_eq_depth",
			"_gte_created_at",
			"_lte_created_at",
			"_free_text",
			"_in_members",
			"_gte_updated_at",
			"_lte_updated_at",
			"_like_tags",
			"_mlikeor_tags",
			"_mlikeand_tags",
			"_eq_appear_in_list",
			"_eq_privacy",
			"_in_privacy",
			"_eq_inheritance_type",
			"_in_inheritance_type",
			"_eq_status",
			"_in_status",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "full_name", "depth");

		$this->aliases = array ( 
			"owner" => "kuser_id"
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "CategoryFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = categoryPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return categoryPeer::ID;
	}
}

?>
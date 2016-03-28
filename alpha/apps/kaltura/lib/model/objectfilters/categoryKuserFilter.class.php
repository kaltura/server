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
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_status",
				"_in_status",
				"_eq_permission_level",
				"_in_permission_level",
				"_eq_update_method",
				"_in_update_method",
				"_likex_category_full_ids",
				"_eq_category_full_ids",
				"_mlikeor_screen_name-puser_id",
				"_matchor_permission_names",
				"_matchand_permission_names",
				"_notcontains_permission_names",
				"_category_direct_members",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "full_name");
		
		$this->aliases = array ( 
			"user_id" => "kuser_id"
		);
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
	// The base class should invoke $peer_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = categoryKuserPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return categoryKuserPeer::ID;
	}
	
	public function setCategoryIdEqual($v)
	{
		$this->set('_eq_category_id', $v);
	}
	
	public function setUserIdEqual($v)
	{
		$this->set('_eq_user_id', $v);
	}

	public function setFullIdsStartsWith($v)
	{
		$this->set('_likex_category_full_ids', $v);
	}




}

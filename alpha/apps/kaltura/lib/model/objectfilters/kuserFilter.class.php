<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class kuserFilter extends baseObjectFilter
{
	const PUSER_ID_OR_SCREEN_NAME = 'puser_id,screen_name';
	const FIRST_NAME_OR_LAST_NAME = 'full_name,last_name';
	
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue (
			array (
				"_likex_screen_name"   , 
				"_like_screen_name"   ,
				"_like_email",
				"_likex_email" ,
				"_like_country" ,
				"_like_tags" ,
				"_mlikeor_tags",
				"_mlikeand_tags" ,
				"_gte_created_at" ,
				"_lte_created_at" ,
				"_gte_updated_at" ,
				"_lte_updated_at" ,
				"_gte_produced_kshows" ,
				"_gte_entries" ,
				"_eq_partner_id" , 
				"_eq_puser_id",
				"_in_puser_id",
			    "_likex_first_name",
			    "_likex_last_name",
				"_eq_id",
				"_in_id",
				"_eq_status",
				"_in_status",
				"_gte_id" , 
				"_lte_id" ,
				"_notin_id",
				"_gte_login_data_id",
				"_gt_login_data_id",
				"_ltornull_login_data_id",
				"_eq_is_admin",
				"_likex_puser_id_or_screen_name",
				'_likex_first_name_or_last_name',	
				'_mlikeand_permission_names',
				'_mlikeor_permission_names',
				'_eq_role_ids',		
				'_in_role_ids',
				'_eq_type',
				'_in_type'
			) ,
			NULL
		);
			
		$this->allowed_order_fields = array("puser_id", "created_at", "updated_at");
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "UserFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = kuserPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return kuserPeer::ID;
	}
}

?>

<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class AssetFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_entry_id",
			"_in_entry_id",
		    "_eq_flavor_params_id",
		 	"_in_flavor_params_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_format",
			"_in_format",
			"_eq_container_format",
			"_in_container_format",
			"_eq_status",
			"_in_status",
			"_notin_status",
			"_gte_size",
			"_lte_size",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_deleted_at",
			"_lte_deleted_at",
			"_like_tags" ,
			"_mlikeor_tags" ,			
			"_mlikeand_tags" ,
			"_in_type",
			"_notin_type",
		) , NULL );

		$this->allowed_order_fields = array ( 
			"created_at",
			"updated_at",
			"deleted_at",
			"size",
		);
			
		$this->aliases = array ( 
			"format" => "container_format",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "AssetFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = assetPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return assetPeer::ID;
	}
}


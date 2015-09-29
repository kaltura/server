<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class ServerNodeFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_heartbeat_time",
			"_lte_heartbeat_time",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_system_name",
			"_in_system_name",
			"_eq_status",
			"_in_status",
			"_eq_type",
			"_in_type",
			"_like_tags" ,
			"_mlikeor_tags" ,
			"_mlikeand_tags",
			"_like_host_name",
			"_mlikeor_host_name",
			"_mlikeand_host_name",
			"_like_playback_host_name",
			"_mlikeor_playback_host_name",
			"_mlikeand_playback_host_name",
			"_eq_dc",
			"_in_dc",
			"_eq_parent_id",
			"_in_parent_id",
			) , NULL );

		$this->allowed_order_fields = array ( 
			"created_at",
			"updated_at",
			"heartbeat_time",
		);
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ServerNodeFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = ServerNodePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return ServerNodePeer::ID;
	}
}



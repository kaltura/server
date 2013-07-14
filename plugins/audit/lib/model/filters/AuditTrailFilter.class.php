<?php
/**
 * @package plugins.audit
 * @subpackage model.filters
 */
class AuditTrailFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_parsed_at",
				"_lte_parsed_at",
				"_eq_status",
				"_in_status",
				"_eq_object_type",
				"_in_object_type",
				"_eq_object_id",
				"_in_object_id",
				"_eq_related_object_id",
				"_in_related_object_id",
				"_eq_related_object_type",
				"_in_related_object_type",
				"_eq_entry_id",
				"_in_entry_id",
				"_eq_master_partner_id",
				"_in_master_partner_id",
				"_eq_partner_id",
				"_in_partner_id",
				"_eq_request_id",
				"_in_request_id",
				"_eq_user_id",
				"_in_user_id",
				"_eq_action",
				"_in_action",
				"_eq_context",
				"_in_context",
				"_eq_entry_point",
				"_in_entry_point",
				"_eq_server_name",
				"_in_server_name",
				"_eq_ip_address",
				"_in_ip_address",
				"_eq_ks", 
				"_eq_audit_object_type", 
				"_eq_client_tag", 
				"_in_audit_object_type"
			) , NULL );

		$this->allowed_order_fields = array ("created_at" , "parsed_at");
			
		$this->aliases = array ( 
			"user_id" => "kuser_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "AuditTrailFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = AuditTrailPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return AuditTrailPeer::ID;
	}
}

?>
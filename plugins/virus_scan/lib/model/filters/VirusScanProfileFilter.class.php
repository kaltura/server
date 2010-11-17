<?php

class VirusScanProfileFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_id",
				"_in_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_partner_id",
				"_in_partner_id",
				"_eq_status",
				"_in_status",
				"_eq_engine_type",
				"_in_engine_type",
			) , NULL );

		$this->allowed_order_fields = array ("created_at" , "updated_at");
			
		$this->aliases = array ( 
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "VirusScanProfileFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = VirusScanProfilePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return VirusScanProfilePeer::ID;
	}
}


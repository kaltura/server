<?php

class ShortLinkFilter extends baseObjectFilter
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
				"_gte_expires_at",
				"_lte_expires_at",
				"_eq_partner_id",
				"_in_partner_id",
				"_eq_user_id",
				"_in_user_id",
				"_eq_system_name",
				"_in_system_name",
				"_eq_status",
				"_in_status",
			) , NULL );

		$this->allowed_order_fields = array("created_at" , "updated_at", "expires_at");
			
		$this->aliases = array(
			"user_id" => "kuser_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ShortLinkFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = ShortLinkPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return ShortLinkPeer::ID;
	}
}


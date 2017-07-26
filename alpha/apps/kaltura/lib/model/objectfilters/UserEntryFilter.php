<?php

/**
 * @package Core
 * @subpackage model.filters
 */
class UserEntryFilter extends baseObjectFilter
{
	protected function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_notin_id",
			"_eq_entry_id",
			"_in_entry_id",
			"_notin_entry_id",
			"_eq_user_id",
			"_in_user_id",
			"_notin_user_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_status",
			"_in_status",
			"_eq_type",
			"_eq_extended_status",
			"_in_extended_status",
			"_notin_extended_status",
			"_eq_privacy_context",
			"_in_privacy_context",
			"_in_partner_id",
			"_eq_partner_id",
		) , NULL );

		$this->allowed_order_fields = array (
			"created_at",
			"updated_at",
		);

		$this->aliases = array ( 
			"user_id" => "kuser_id",
			);
	}

	protected function getFieldNameFromPeer ( $field_name )
	{
		$res = UserEntryPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}
	protected function getIdFromPeer (  )
	{
        return UserEntryPeer::ID;
	}
}
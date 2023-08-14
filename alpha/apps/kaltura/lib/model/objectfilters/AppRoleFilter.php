<?php

/**
 * @package Core
 * @subpackage model.filters
 */
class AppRoleFilter extends baseObjectFilter
{
	protected function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			"_eq_user_id",
			"_in_user_id",
			"_eq_app_guid",
			"_in_app_guid",
			"_eq_user_role_id",
			"_in_user_role_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
		),
			NULL);
		
		// todo add order by got app_guid and kuser_id
		$this->allowed_order_fields = array(
			"created_at",
			"updated_at",
		);
		
		$this->aliases = array(
			"user_id" => "kuser_id",
		);
	}
	
	protected function getFieldNameFromPeer($field_name)
	{
		return KuserToUserRolePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	
	protected function getIdFromPeer()
	{
		return KuserToUserRolePeer::ID;
	}
}

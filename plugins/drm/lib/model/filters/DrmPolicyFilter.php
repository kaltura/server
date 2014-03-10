<?php
/**
 * @package plugins.drm
 * @subpackage model.filters
 */
class DrmPolicyFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_like_name",
			"_like_system_name",
			"_eq_provider",
			"_in_provider",
			"_eq_status",
			"_in_status",
			"_eq_scenario",
			"_in_scenario",
			) , null );

		$this->allowed_order_fields = array (
			"id",
			"name",
			"system_name",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "DrmPolicyFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = DrmPolicyPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return DrmPolicyPeer::ID;
	}
}

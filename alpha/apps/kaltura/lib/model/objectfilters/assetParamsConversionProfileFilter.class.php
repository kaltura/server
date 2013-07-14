<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class assetParamsConversionProfileFilter extends baseObjectFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			"_eq_conversion_profile_id",
			"_in_conversion_profile_id",
			"_eq_asset_params_id",
			"_in_asset_params_id",
			"_eq_ready_behavior",
			"_in_ready_behavior",
			"_eq_origin",
			"_in_origin",
			"_eq_system_name",
			"_in_system_name",
			"_eq_force_none_complied",
			) , NULL );

		$this->allowed_order_fields = array(
			"created_at", 
			"updated_at"
		);
		
		$this->aliases = array(
			"asset_params_id" => "flavor_params_id",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "assetParamsConversionProfileFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = flavorParamsConversionProfilePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return flavorParamsConversionProfilePeer::ID;
	}
}

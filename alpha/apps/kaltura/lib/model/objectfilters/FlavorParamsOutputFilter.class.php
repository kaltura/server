<?php
require_once( 'model/objectfilters/filters.class.php');

class FlavorParamsOutputFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_is_system_default",
			"_gte_created_at",
			"_lte_created_at",
			"_eq_flavor_params_id",
			"_eq_flavor_params_version",
			"_eq_flavor_asset_id",
			"_eq_flavor_asset_version",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at")	;
		
		$this->aliases = array("is_system_default" => "is_default");
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "FlavorParamsOutputFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = flavorParamsOutputPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return flavorParamsOutputPeer::ID;
	}
}

?>
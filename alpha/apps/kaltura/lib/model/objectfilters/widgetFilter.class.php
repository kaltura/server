<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class widgetFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id" , 
			"_eq_source_widget_id",
			"_eq_root_widget_id",
			"_eq_entry_id",
			"_eq_ui_conf_id",
			"_like_partner_data",
			"_gte_created_at" ,
			"_lte_created_at" ,
			"_gte_updated_at" ,
			"_lte_updated_at" ,
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "_eq_ui_conf_id" )	;
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "widgetFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = widgetPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return widgetPeer::ID;
	}
}

?>
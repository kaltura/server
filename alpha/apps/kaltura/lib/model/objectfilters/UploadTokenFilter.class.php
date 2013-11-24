<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class UploadTokenFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_user_id",
			"_eq_status",
			"_in_status",
			"_eq_file_name",
			"_eq_file_size",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" );
		
		$this->aliases = array ( 
			"user_id" => "kuser_id",
		);
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "UploadTokenFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = UploadTokenPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return UploadTokenPeer::ID;
	}
}

?>
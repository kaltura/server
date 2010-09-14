<?php
require_once( 'model/objectfilters/filters.class.php');

class MediaInfoFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_flavor_asset_id",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at")	;
		
		$this->aliases = array("is_system_default" => "is_default");
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "MediaInfoFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = mediaInfoPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return mediaInfoPeer::ID;
	}
}

?>
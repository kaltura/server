<?php
require_once( 'model/objectfilters/filters.class.php');
require_once( 'model/moderationPeer.php');

class moderationFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id" , 
			"_eq_puser_id" ,
			"_eq_status" ,
			"_in_status" ,
			"_like_comments" ,
			"_eq_object_id" ,			
			"_eq_object_type" ,
			"_eq_group_id" ,
			) , NULL );
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ModerationFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = moderationPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return moderationPeer::ID;
	}
}

?>
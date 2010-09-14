<?php
require_once( 'model/objectfilters/filters.class.php');
require_once( 'model/kshowPeer.php');

class kshowFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_like_name" ,
//			"_like_description" ,
			"_like_tags" ,
			"_mlikeor_tags" ,			
			"_mlikeand_tags" ,			
//			"_like_category" ,
//			"_gte_votes" ,
			"_gte_views" ,
			"_eq_type" ,
			"_eq_producer_id" , 	// for specific producer
			"_gte_created_at" ,
			"_lte_created_at" ,
			"_bitand_status" ,
			"_eq_indexed_custom_data_3" ,
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "views", "rank")	;
	}

	public function describe()
	{
		return
			array (
				"display_name" => "KShowFilter",
				"desc" => ""
			);
	}

	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		return kshowPeer::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer (  )
	{
		return kshowPeer::ID;
	}
}
?>
<?php
/**
 * @package plugins.rating
 * @subpackage model.filters
 */

class RatingFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_entry_id",
			"_eq_user_id",
			"_in_entry_id",
			"_eq_rank",
			"in_rank",
		) , NULL );
		
		$this->allowed_order_fields = array (
			'created_at',
		);
		
		$this->aliases = array ();
	}
	
	public function describe()
	{
		return
			array (
				"display_name" => "RatingFilter",
				"desc" => ""
			);
	}
	
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = kvotePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}
	
	public function getIdFromPeer (  )
	{
		return kvotePeer::ID;
	}
}
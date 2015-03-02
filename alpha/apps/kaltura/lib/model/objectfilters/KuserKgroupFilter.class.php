<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class KuserKgroupFilter extends baseObjectFilter
{
	public function init ()
	{
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_group_id",
				"_in_group_id",
				"_eq_user_id",
				"_in_user_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_status",
				"_in_status",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at");
		
		$this->aliases = array ( 
			"user_id" => "kuser_id",
			"group_id" => "kgroup_id"
		);
	}

	public function describe()
	{
		return
			array (
				"display_name" => "KuserKgroupFilter",
				"desc" => ""
			);
	}

	// The base class should invoke $peer_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = KuserKgroupPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return KuserKgroupPeer::ID;
	}


	public function setGroupIdEqual($v)
	{
		$this->set('_eq_group_id', $v);
	}
}

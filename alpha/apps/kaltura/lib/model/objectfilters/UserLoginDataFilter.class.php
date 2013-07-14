<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class UserLoginDataFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_login_email",
			) , NULL );

		$this->allowed_order_fields = array ();
		$this->aliases = array ();
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "UserLoginDataFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = UserLoginDataPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return UserLoginDataPeer::ID;
	}
}

?>
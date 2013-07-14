<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class accessControlFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
	 * @see myBaseObject::init()
	 */
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_system_name",
			"_in_system_name"
			) , NULL );

		$this->allowed_order_fields = array ("created_at", "updated_at");
			
	}

	/**
	 * @return array 
	 */
	public function describe() 
	{
		return 
			array (
				"display_name" => "AccessControlFilter",
				"desc" => ""
			);
	}
	
	/* (non-PHPdoc)
	 * @see baseObjectFilter::getFieldNameFromPeer()
	 */
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = accessControlPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	public function getIdFromPeer (  )
	{
		return accessControlPeer::ID;
	}
}

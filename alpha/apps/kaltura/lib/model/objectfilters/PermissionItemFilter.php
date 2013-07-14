<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class PermissionItemFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				'_eq_id',
				'_in_id',
				'_eq_type',
				'_in_type',
				'_eq_partner_id',
				'_in_partner_id',
				'_mlikeor_permission_names' ,
				'_mlikeand_permission_names',
				'_mlikeor_tags',
				'_mlikeand_tags',
				'_gte_created_at',
				'_lte_created_at',
				'_gte_updated_at',
				'_lte_updated_at',			
			) , NULL );

		$this->allowed_order_fields = array ('id', 'created_at', 'updated_at');
			
		$this->aliases = array ( 
		);
	}

	public function describe() 
	{
		return 
			array (
				'display_name' => 'PermissionItemFilter',
				'desc' => '',
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = PermissionItemPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return PermissionItemPeer::ID;
	}
}


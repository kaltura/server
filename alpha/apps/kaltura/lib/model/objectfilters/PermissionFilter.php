<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class PermissionFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				'_eq_id',
				'_in_id',
				'_eq_name',
				'_in_name',
				'_eq_type',
				'_in_type',
				'_like_friendly_name',
				'_like_description',
				'_eq_status',
				'_in_status',
				'_eq_partner_id',
				'_in_partner_id',
				'_mlikeor_tags' ,
				'_mlikeand_tags',
				'_gte_created_at',
				'_lte_created_at',
				'_gte_updated_at',
				'_lte_updated_at',
			) , NULL );

		$this->allowed_order_fields = array ('id', 'name', 'created_at' , 'updated_at');
			
		$this->aliases = array ( 
		);
	}

	public function describe() 
	{
		return 
			array (
				'display_name' => 'PermissionFilter',
				'desc' => '',
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = PermissionPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return PermissionPeer::ID;
	}
}


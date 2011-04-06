<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.filters
 */
class DropFolderFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_like_name",
			"_eq_type",
			"_in_type",
			"_eq_status",
			"_in_status",
			"_eq_conversion_profile_id",
			"_in_conversion_profile_id",
			"_eq_dc",
			"_in_dc",
			"_like_path",
			"_like_slug_field",
			"_like_slug_regex",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			) , null );

		$this->allowed_order_fields = array ("created_at", "updated_at", "id", "name");
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "DropFolderFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = DropFolderPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return DropFolderPeer::ID;
	}
}

<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.filters
 */
class DropFolderFileFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_entry_id",
			"_eq_drop_folder_id",
			"_in_drop_folder_id",
			"_like_file_name",
			"_eq_file_name",
			"_in_file_name",
			"_eq_status",
			"_in_status",
			"_notin_status",
			"_like_parsed_slug",
			"_eq_parsed_slug",
			"_in_parsed_slug",
			"_like_parsed_flavor",
			"_eq_parsed_flavor",
			"_in_parsed_flavor",
			"_eq_error_code",
			"_in_error_code",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_lead_drop_folder_file_id",
			"_eq_deleted_drop_folder_file_id",
			) , null );

		$this->allowed_order_fields = array (
			"created_at",
			"updated_at",
			"id",
			"file_name",
			"file_size",
			"file_size_last_set_at",
			"parsed_slug",
			"parsed_flavor",
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "DropFolderFileFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = DropFolderFilePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return DropFolderFilePeer::ID;
	}
}

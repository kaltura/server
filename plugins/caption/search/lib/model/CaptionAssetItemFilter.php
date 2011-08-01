<?php
/**
 * @package plugins.captionSearch
 * @subpackage model.filters
 */ 
class CaptionAssetItemFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			"_eq_id",
			"_in_id",
			"_eq_entry_id",
			"_in_entry_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_status",
			"_in_status",
			"_notin_status",
			"_gte_size",
			"_lte_size",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_deleted_at",
			"_lte_deleted_at",
			"_like_tags" ,
			"_mlikeor_tags" ,			
			"_mlikeand_tags" ,
			"_like_content"   ,			
			"_mlikeor_content" ,			
			"_mlikeand_content" ,
			"_like_partner_description"   ,			
			"_mlikeor_partner_description" ,			
			"_mlikeand_partner_description" ,
			"_eq_language",
			"_in_language",
			"_eq_label",
			"_in_label",
			"_lte_start_date",
			"_gte_start_date",
			"_lte_end_date",
			"_gte_end_date",
		), null);

		$this->allowed_order_fields = array(
			"created_at",
			"updated_at",
			"deleted_at",
			"size",
			"start_date",
			"end_date",
		);

		$this->aliases = array();
	}

	public function describe()
	{
		return array (
			"display_name" => "CaptionAssetItemFilter",
			"desc" => "",
			"fields" => array()
		);
	}

	public function getFieldNameFromPeer ( $field_name )
	{
		return CaptionAssetItemPeer::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer()
	{
		return entryPeer::ID;
	}
	
		
	public static function hasMachableField ( $field_name )
	{
		return in_array($field_name, array(
			"content", 
			"partner_description", 
			"tags", 
			"language", 
			"label", 
		));
	}
	
	public function setEntryIdIn(array $arr)
	{
		$this->set('_in_entry_id', $arr);
	}
}


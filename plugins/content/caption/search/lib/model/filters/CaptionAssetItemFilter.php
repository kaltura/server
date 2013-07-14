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
			"_eq_caption_asset_id",
			"_in_caption_asset_id",
			"_eq_entry_id",
			"_in_entry_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_format",
			"_in_format",
			"_eq_status",
			"_in_status",
			"_notin_status",
			"_gte_size",
			"_lte_size",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
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
			"_gte_start_time",
			"_lte_start_time",
			"_gte_end_time",
			"_lte_end_time",
			"_in_id", 
			"_gte_deleted_at", 
			"_eq_id", 
			"_lte_deleted_at",
		    "_eq_flavor_params_id",
		 	"_in_flavor_params_id",
		), null);

		$this->allowed_order_fields = array(
			"created_at",
			"updated_at",
			"size",
			"start_time",
			"end_time",
		);

		$this->aliases = array(
		);
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
		if($field_name == 'partner_description')
			return CaptionAssetItemPeer::PARTNER_DESCRIPTION;
		if($field_name == 'language')
			return CaptionAssetItemPeer::LANGUAGE;
		if($field_name == 'label')
			return CaptionAssetItemPeer::LABEL;
		if($field_name == 'status')
			return CaptionAssetItemPeer::STATUS;
		if($field_name == 'size')
			return CaptionAssetItemPeer::SIZE;
		if($field_name == 'updated_at')
			return CaptionAssetItemPeer::UPDATED_AT;
		if($field_name == 'format')
			return CaptionAssetItemPeer::FORMAT;
			
		return CaptionAssetItemPeer::translateFieldName($field_name, BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME);
	}

	public function getIdFromPeer()
	{
		return CaptionAssetItemPeer::ID;
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
		$ids = $this->get('_in_entry_id');
		if($ids)
		{
			if(!is_array($ids))
				$ids = explode(',', $ids);
	
			$arr = array_merge($ids, $arr);
		}
		
		$this->set('_in_entry_id', $arr);
	}
}


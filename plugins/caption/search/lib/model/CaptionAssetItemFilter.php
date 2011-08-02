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

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('PartnerDescription' => -1, 'Language' => -2, 'Label' => -3, 'Status' => -4, 'Size' => -5, 'UpdatedAt' => -6),
		BasePeer::TYPE_STUDLYPHPNAME => array ('partnerDescription' => -1, 'language' => -2, 'label' => -3, 'status' => -4, 'size' => -5, 'updatedAt' => -6),
		BasePeer::TYPE_COLNAME => array (self::PARTNER_DESCRIPTION => -1, self::LANGUAGE => -2, self::LABEL => -3, self::STATUS => -4, self::SIZE => -5, self::UPDATED_AT => -6),
		BasePeer::TYPE_FIELDNAME => array ('partner_description' => -1, 'language' => -2, 'label' => -3, 'status' => -4, 'size' => -5, 'updated_at' => -6),
		BasePeer::TYPE_NUM => array (-1, -2, -3, -4, -5, -6)
	);
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$toNames = self::getFieldNames(BasePeer::TYPE_COLNAME);
		$key = isset(self::$fieldKeys[BasePeer::TYPE_FIELDNAME][$field_name]) ? self::$fieldKeys[BasePeer::TYPE_FIELDNAME][$field_name] : null;
		if ($key === null)
			return CaptionAssetItemPeer::translateFieldName($field_name, BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME);
			
		return $toNames[$key];
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


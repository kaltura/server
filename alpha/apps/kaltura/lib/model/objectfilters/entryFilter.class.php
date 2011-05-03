<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class entryFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'name,tags,description,entry_id';
	
	// allow only 256 charaters when creation a MATCH-AGAINST caluse
	const MAX_SAERCH_TEXT_SIZE = 256;
	
	// allow no more than 100 values in IN and NOT_IN clause
	const MAX_IN_VALUES = 100;
	
	// this flag will indicate if the uiser_id set in the _eq_user_id field shouyld be translated to kuser_id or not.
	// if $user_id_is_kuser_id is true, the switch was already done   
	public $user_id_is_kuser_id = false;
	
	public function setSwitchUserIdToKuserId( $kuser_id )
	{
		$this->user_id_is_kuser_id = true;
		$this->fields["_eq_user_id"] = $kuser_id;
	}
	 
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_in_id" , 
			"_notin_id" , 
			"_eq_id" , 
			"_eq_user_id" ,  // is in fact the kuser_id - see aliases
			"_eq_kshow_id" ,
			"_eq_status" ,
			"_in_status" ,
			"_notin_status" ,
			"_not_status" ,
			"_eq_type"   ,
			"_in_type"   ,
			"_eq_media_type"   ,
			"_in_media_type"   ,
			"_eq_indexed_custom_data_1"   ,
			"_in_indexed_custom_data_1"   ,			
			"_like_name"   ,
			"_eq_name"   ,
			"_eq_tags" ,			
			"_like_tags" ,
			"_mlikeor_tags" ,			
			"_mlikeand_tags" ,
			"_mlikeor_admin_tags" ,			
			"_mlikeand_admin_tags" ,
			"_like_admin_tags" ,			
			"_mlikeor_name" ,			
			"_mlikeand_name" ,
			"_mlikeor_search_text" ,			
			"_mlikeand_search_text" ,			
//			"_gte_votes" ,
			"_eq_group_id" ,
			"_gte_views" ,
			"_gte_created_at" ,
			"_lte_created_at" ,
			"_gte_updated_at" ,
			"_lte_updated_at" ,
			"_gte_modified_at" ,
			"_lte_modified_at" ,
			"_in_partner_id"   ,
			"_eq_partner_id" ,
			"_eq_source_link" ,
			"_gte_media_date" ,
			"_lte_media_date" ,
			"_eq_moderation_status" , 
			"_in_moderation_status" ,
			"_notin_moderation_status" ,
			"_not_moderation_status" ,
			"_eq_display_in_search" ,	
			"_in_display_in_search" ,
			"_mlikeor_tags-name" ,
			"_mlikeor_tags-admin_tags" ,
			"_mlikeor_tags-admin_tags-name" ,
			"_mlikeand_tags" ,
			"_mlikeand_tags-name" ,	
			"_mlikeand_tags-admin_tags" ,
			"_mlikeand_tags-admin_tags-name" ,			
			"_matchand_search_text" ,
			"_matchor_search_text" ,
			"_matchand_categories", // see alias (this filter also being used in category::save(), so make sure it is not changed or removed!)
			"_matchor_categories", // see alias
			"_matchand_categories_ids", // see alias
			"_matchor_categories_ids", // see alias
			"_matchand_flavor_params_ids",
			"_matchor_flavor_params_ids",
			"_matchor_duration_type", // see alias
			"_eq_document_type", // for document listing in api_v3
			"_in_document_type", // for document listing in api_v3
			"_lt_duration",
			"_gt_duration",
			"_lte_duration",
			"_gte_duration",
			"_lteornull_start_date",
			"_gteornull_start_date",
			"_lte_start_date",
			"_gte_start_date",
			"_lteornull_end_date",
			"_gteornull_end_date",
			"_lte_end_date",
			"_gte_end_date",
			"_lte_available_from",
			"_gte_available_from",
			"_eq_access_control_id",
			"_in_access_control_id",
			"_free_text",
			"_eq_reference_id",
			"_in_reference_id",
			"_eq_replacing_entry_id",
			"_in_replacing_entry_id",
			"_eq_replaced_entry_id",
			"_in_replaced_entry_id",
			"_eq_replacement_status",
			"_in_replacement_status",
			"_gte_partner_sort_value",
			"_lte_partner_sort_value",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at" , "views", "name", "media_date" , 
			"type" , "media_type" , "plays" , "views" , "rank" , "moderation_count" , "moderation_status" , "modified_at", "available_from", "duration" , "partner_sort_value")	;

		$this->aliases = array ( 
			"user_id" => "kuser_id",
			"document_type" => "media_type", // for document listing in api_v3
			"duration" => "length_in_msecs",
			"categories" => "search_text_discrete",
			"categories_ids" => "search_text_discrete",
			"duration_type" => "search_text_discrete",
			"flavor_params_ids" => "search_text_discrete",
		);
	}

	public function describe()
	{
		return
			array (
				"display_name" => "EntryFilter",
				"desc" => "",
				"fields" => array(
					"user_id" => array("type" => "integer", "desc" => ""),
					"kshow_id" => array("type" => "integer", "desc" => ""),
					"type" => array("type" => "enum,entry,ENTRY_TYPE", "desc" => ""),
					"media_type" => array("type" => "enum,entry,ENTRY_MEDIA_TYPE", "desc" => ""),
					"view" => array("type" => "integer", "desc" => ""),
					"created_at" => array("type" => "date", "desc" => ""),
					"updated_at" => array("type" => "date", "desc" => "")
				)
			);
	}

	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		if($field_name == 'replacement_status')
			return 'entry.REPLACEMENT_STATUS';
			
		return entryPeer::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer (  )
	{
		return entryPeer::ID;
	}
	
		
	public static function hasMachableField ( $field_name )
	{
		return in_array($field_name, array(
			"name", 
			"description", 
			"tags", 
			"admin_tags", 
			"categories_ids", 
			"flavor_params_ids", 
			"duration_type", 
			"reference_id", 
			"replacing_entry_id", 
			"replaced_entry_id",
		));
	}
	
	
	public static function categoryNamesToIndexedIds($cats)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
			
		$catsIds = array();
		foreach($cats as $cat)
		{
			$categories = categoryPeer::getByFullNameWildcardMatch($cat);
			if (count($categories) == 0)
			{
				$catsIds[mySearchUtils::ENTRY_CATEGORY_ID_PREFIX . "NO_FOUND"] = null;
			}
			else
			{
				foreach($categories as $category)
				{
					$catsIds[mySearchUtils::ENTRY_CATEGORY_ID_PREFIX . $category->getId()] = null;
				}
			}
		}
		return implode(",", array_keys($catsIds));
	}
	
	/**
	 * Convert the categories to categories ids
	 * 
	 * @param string $cats Categories full names
	 * @return string Categogories indexes ids
	 */
	public static function categoryNamesToIds($cats)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
			
		$catsIds = array();
		foreach($cats as $cat)
		{
			$categories = categoryPeer::getByFullNameWildcardMatch($cat);
			foreach($categories as $category)
				$catsIds[] = $category->getId();
		}
		return implode(",", $catsIds);
	}
	
	/**
	 * Convert the flavor params ids to indexed flavor params string
	 * 
	 * @param string $flavorParamsIds
	 * @return string
	 */
	public function flavorParamsIdsToIndexedStrings($flavorParamsIds)
	{ 
		if (is_null($flavorParamsIds) || $flavorParamsIds === "") // string "0" is valid here
			$flavorParamsIds = array();
		else
			$flavorParamsIds = explode(",", $flavorParamsIds);
		kArray::trim($flavorParamsIds);
			
		$flavorParamsStrings = array();
		foreach($flavorParamsIds as $flavorParamsId)
		{
			$flavorParamsStrings[mySearchUtils::ENTRY_FLAVOR_PARAMS_PREFIX.$flavorParamsId] = null;
		}
		return implode(",", array_keys($flavorParamsStrings));
	}
	
	/**
	 * Convert the duration types to indexed duration type strings
	 * 
	 * @param string $durationTypeIds
	 * @return string
	 */
	public function durationTypesToIndexedStrings($durationTypeIds)
	{ 
		if (is_null($durationTypeIds) || $durationTypeIds === "") // string "0" is valid here
			$durationTypeIds = array();
		else
			$durationTypeIds = explode(",", $durationTypeIds);
		kArray::trim($durationTypeIds);
			
		$durationTypesStrings = array();
		foreach($durationTypeIds as $durationTypeId)
		{
			$durationTypesStrings[mySearchUtils::ENTRY_DURATION_TYPE_PREFIX.$durationTypeId] = null;
		}
		return implode(",", array_keys($durationTypesStrings));
	}
	
	/**
	 * Will be used for the KCW's search - this is slightly different  
	 *
	 * @param Criteria $criteria
	 * @param array $keys_to_search
	 * @param array $field_names
	 */
	public function addSearchMatchToCriteria ( $criteria , $keys_to_search , $field_names  )
	{
		$freeText = $this->get('_free_text');
		if($freeText)
		{
			$freeText .= " $keys_to_search";
		}
		else 
		{
			$freeText = $keys_to_search;
		}
		$this->set('_free_text', $freeText);
		
		$this->attachToCriteria($criteria);
	}
	
	public function setIdEquel($v)
	{
		$this->set('_eq_id', $v);
	}
	
	public function setIdIn(array $arr)
	{
		$this->set('_in_id', $arr);
	}
	
	public function setIdNotIn(array $arr)
	{
		$this->set('_notin_id', $arr);
	}
	
	public function setStatusEquel($v)
	{
		$this->set('_eq_status', $v);
	}
	
	public function setStatusNot($v)
	{
		$this->set('_not_status', $v);
	}
	
	public function setStatusIn(array $arr)
	{
		$this->set('_in_status', $arr);
	}
	
	public function setStatusNotIn(array $arr)
	{
		$this->set('_notin_status', $arr);
	}
	
	public function setUserIdEquel($v)
	{
		$this->set('_eq_user_id', $v);
	}
	
	public function setTypeEquel($v)
	{
		$this->set('_eq_type', $v);
	}
	
	public function setTypeIn(array $arr)
	{
		$this->set('_in_type', $arr);
	}
	
	public function setMediaTypeEquel($v)
	{
		$this->set('_eq_media_type', $v);
	}
	
	public function setMediaTypeIn(array $arr)
	{
		$this->set('_in_media_type', $arr);
	}
	
	public function setModerationStatusEquel($v)
	{
		$this->set('_eq_moderation_status', $v);
	}
	
	public function setModerationStatusNot($v)
	{
		$this->set('_not_moderation_status', $v);
	}
	
	public function setModerationStatusIn(array $arr)
	{
		$this->set('_in_moderation_status', $arr);
	}
	
	public function setModerationStatusNotIn(array $arr)
	{
		$this->set('_notin_moderation_status', $arr);
	}
	
	public function setDurationLessThan($v)
	{
		$this->set('_lt_duration', $v);
	}
	
	public function setDurationGreaterThan($v)
	{
		$this->set('_gt_duration', $v);
	}
	
	public function setDurationLessThanOrEquel($v)
	{
		$this->set('_lte_duration', $v);
	}
	
	public function setDurationGreaterThanOrEquel($v)
	{
		$this->set('_gte_duration', $v);
	}
	
	public function setDisplayInSearchEquel($v)
	{
		$this->set('_eq_display_in_search', $v);
	}
	
	public function getDisplayInSearchEquel()
	{
		$this->get('_eq_display_in_search');
	}
	
	public function unsetDisplayInSearchEquel()
	{
		$this->unsetByName('_eq_display_in_search');
	}
	
	public function setPartnerIdEquel($v)
	{
		$this->set('_eq_partner_id', $v);
	}
	
	public function setFlavorParamsMatchOr($v)
	{
		$this->set('_matchor_flavor_params_ids', $v);
	}
	
}


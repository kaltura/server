<?php
class entryFilter extends baseObjectFilter
{
	public static $sphinxFields = array(
		entryPeer::ID => 'int_entry_id',
		entryPeer::NAME => 'name',
		entryPeer::TAGS => 'tags',
		entryPeer::CATEGORIES_IDS => 'categories',
		entryPeer::FLAVOR_PARAMS_IDS => 'flavor_params',
		entryPeer::SOURCE_LINK => 'source_link',
		entryPeer::KSHOW_ID => 'kshow_id',
		entryPeer::GROUP_ID => 'group_id',
		entryPeer::DESCRIPTION => 'description',
		entryPeer::ADMIN_TAGS => 'admin_tags',
		'plugins_data',
		'entry.DURATION_TYPE' => 'duration_type',
		
		entryPeer::KUSER_ID => 'kuser_id',
		entryPeer::STATUS => 'entry_status',
		entryPeer::TYPE => 'type',
		entryPeer::MEDIA_TYPE => 'media_type',
		entryPeer::VIEWS => 'views',
		entryPeer::PARTNER_ID => 'partner_id',
		entryPeer::MODERATION_STATUS => 'moderation_status',
		entryPeer::DISPLAY_IN_SEARCH => 'display_in_search',
		entryPeer::LENGTH_IN_MSECS => 'duration',
		entryPeer::ACCESS_CONTROL_ID => 'access_control_id',
		entryPeer::MODERATION_COUNT => 'moderation_count',
		entryPeer::RANK => 'rank',
		entryPeer::PLAYS => 'plays',
		
		entryPeer::CREATED_AT => 'created_at',
		entryPeer::UPDATED_AT => 'updated_at',
		entryPeer::MODIFIED_AT => 'modified_at',
		entryPeer::MEDIA_DATE => 'media_date',
		entryPeer::START_DATE => 'start_date',
		entryPeer::END_DATE => 'end_date',
		entryPeer::AVAILABLE_FROM => 'available_from',
	);
	
	public static $sphinxOrderFields = array(
		entryPeer::NAME => 'sort_name',
		
		entryPeer::KUSER_ID => 'kuser_id',
		entryPeer::STATUS => 'entry_status',
		entryPeer::TYPE => 'type',
		entryPeer::MEDIA_TYPE => 'media_type',
		entryPeer::VIEWS => 'views',
		entryPeer::PARTNER_ID => 'partner_id',
		entryPeer::MODERATION_STATUS => 'moderation_status',
		entryPeer::DISPLAY_IN_SEARCH => 'display_in_search',
		entryPeer::LENGTH_IN_MSECS => 'duration',
		entryPeer::ACCESS_CONTROL_ID => 'access_control_id',
		entryPeer::MODERATION_COUNT => 'moderation_count',
		entryPeer::RANK => 'rank',
		entryPeer::PLAYS => 'plays',
		
		entryPeer::CREATED_AT => 'created_at',
		entryPeer::UPDATED_AT => 'updated_at',
		entryPeer::MODIFIED_AT => 'modified_at',
		entryPeer::MEDIA_DATE => 'media_date',
		entryPeer::START_DATE => 'start_date',
		entryPeer::END_DATE => 'end_date',
		entryPeer::AVAILABLE_FROM => 'available_from',
	);
	
	public static $sphinxTypes = array(
		'entry_id' => 'string',
		'name' => 'string',
		'tags' => 'string',
		'categories' => 'string',
		'flavor_params' => 'string',
		'source_link' => 'string',
		'kshow_id' => 'string',
		'group_id' => 'string',
		'metadata' => 'string',
		'duration_type' => 'string',
		
		'int_entry_id' => 'int',
		'kuser_id' => 'int',
		'entry_status' => 'int',
		'type' => 'int',
		'media_type' => 'int',
		'views' => 'int',
		'partner_id' => 'int',
		'moderation_status' => 'int',
		'display_in_search' => 'int',
		'duration' => 'int',
		'access_control_id' => 'int',
		'moderation_count' => 'int',
		'rank' => 'int',
		'plays' => 'int',
		
		'created_at' => 'timestamp',
		'updated_at' => 'timestamp',
		'modified_at' => 'timestamp',
		'media_date' => 'timestamp',
		'start_date' => 'timestamp',
		'end_date' => 'timestamp',
		'available_from' => 'timestamp',
	);
	
	
	// allow only 256 charaters when creation a MATCH-AGAINST caluse
	const MAX_SAERCH_TEXT_SIZE = 256;
	
	// allow no more than 100 values in IN and NOT_IN clause
	const MAX_IN_VALUES = 100;
	
	// if set to true - the MATCH mechanism will replace the LIKE operators 
	private static $force_match = false;
	
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
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "views", "name", "media_date" , 
			"type" , "media_type" , "plays" , "views" , "rank" , "moderation_count" , "moderation_status" , "modified_at", "available_from", "duration" ,)	;

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
					"created_at" => array("type" => "date", "desc" => "")
				)
			);
	}

	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		return entryPeer::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer (  )
	{
		return entryPeer::ID;
	}
	

	public static function forceMatch( $v )
	{
		self::$force_match = $v;
	}
		
	public static function hasMachableField ( $field_name )
	{
		return in_array($field_name, array("name", "description", "tags", "admin_tags", "categories_ids", "flavor_params_ids"));
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
	
	public static function getSphinxFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
			
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
	}
	
	public static function getSphinxFieldType($fieldName)
	{
		if(!isset(self::$sphinxTypes[$fieldName]))
			return null;
			
		return self::$sphinxTypes[$fieldName];
	}
}


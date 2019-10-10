<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class entryFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'name,tags,description,entry_id,reference_id,roots,puser_id,user_names';
	
	// allow only 256 charaters when creation a MATCH-AGAINST caluse
	const MAX_SAERCH_TEXT_SIZE = 256;
	
	// this flag will indicate if the uiser_id set in the _eq_user_id field shouyld be translated to kuser_id or not.
	// if $user_id_is_kuser_id is true, the switch was already done   
	public $user_id_is_kuser_id = false;
	
	public function setSwitchUserIdToKuserId( $kuser_id )
	{
		$this->user_id_is_kuser_id = true;
		$this->fields["_eq_user_id"] = $kuser_id;
	}
	 
	private static $relative_time_fields = array("gte_created_at","lte_created_at","gte_updated_at","lte_updated_at","gte_last_played_at","lte_last_played_at","gte_media_date","lte_media_date","lteornull_start_date","gteornull_start_date","lte_start_date","gte_start_date","lteornull_end_date","gteornull_end_date","lte_end_date","gte_end_date");

	protected function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_in_id" , 
			"_notin_id" , 
			"_eq_id" ,
			"_in_user_id",
			"_notin_user_id",
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
			"_notcontains_categories",
			"_in_categories_full_name",
			"_matchand_categories_ids", // see alias
			"_matchor_categories_ids", // see alias
			"_notcontains_categories_ids",
			"_empty_categories_ids",
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
			"_eq_redirect_from_entry_id",
			"_eq_root_entry_id",
			"_in_root_entry_id",
			"_eq_parent_entry_id",
			"_matchand_entitled_kusers_edit",
			"_matchand_entitled_kusers_publish",
			"_matchand_entitled_kusers_view",
			"_matchor_entitled_kusers_edit",
			"_matchor_entitled_kusers_publish",
			"_matchor_entitled_kusers_view",
			"_is_root",
			"_matchand_roots",
			"_notin_roots",
			"_in_category_entry_status",
			"_in_category_ancestor_id",
			"_eq_creator_id",
			"_lte_total_rank",
			"_gte_total_rank",
			"_gte_last_played_at",
			"_lte_last_played_at",
			"_is_live",
			"_eq_source",
			"_not_source",
			"_in_source",
			"_notin_source",
			"_is_recorded_entry_id_empty",
			"_has_media_server_hostname",
		) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at" , "views", "name", "media_date" , 
			"type" , "media_type" , "plays" , "views" , "rank" , "moderation_count" , "moderation_status" , 
			"modified_at", "available_from", "duration" , "partner_sort_value" , "total_rank", "weight", 
			"start_date", "end_date", "last_played_at", "first_broadcast",
		);

		$this->aliases = array ( 
			"creator_id" => "creator_puser_id",
			"user_id" => "kuser_id",
			"document_type" => "media_type", // for document listing in api_v3
			"duration" => "length_in_msecs",
			"categories" => "search_text_discrete",
			"categories_ids" => "search_text_discrete",
			"duration_type" => "search_text_discrete",
			"reference_id" => "search_text_discrete", 
			"replacing_entry_id" => "search_text_discrete", 
			"replaced_entry_id" => "search_text_discrete",
			"flavor_params_ids" => "search_text_discrete",
			"root_entry_id" => "search_text_discrete", 
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
		try 
		{	
			return entryPeer::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
		}
		catch(PropelException $e)
		{
			return 'entry.' . strtoupper($field_name);
		}
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
			"root_entry_id",
			"parent_entry_id",
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
					/* @var $category category */
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
	 * Convert the categories to categories ids
	 * 
	 * @param string $cats Categories full names
	 * @return string Categogories indexes ids
	 */
	public static function categoryFullNamesToIds($cats)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
			
		$catsIds = array();
		foreach($cats as $cat)
		{
			$category = categoryPeer::getByFullNameExactMatch($cat);
			
			if($category)
				$catsIds[] = $category->getId();
			
			$categories = categoryPeer::getByFullNameWildcardMatch($cat . categoryPeer::CATEGORY_SEPARATOR);
			foreach($categories as $category)
				$catsIds[] = $category->getId();
		}

		return implode(",", $catsIds);
	}	
	
	/**
	 * Convert the categories to categories ids
	 * 
	 * @param string $cats Categories full names
	 * @param string $statuses comma seperated
	 * @return string Categogories indexes ids
	 */
	public static function categoryIdsToIdsParsed($cats, $statuses = null)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
			
		if($statuses == null || trim($statuses) == '')
			$statuses = CategoryEntryStatus::ACTIVE;
		
		$statuses = explode(',', trim($statuses));
		
		$categoryFullIdsToIds = array();
		foreach($cats as $cat)
		{
			$category = categoryPeer::retrieveByPK($cat);
			if(!$category)
				continue;

			foreach ($statuses as $status)
			{
				$categoryFullIdsToIds[] = entry::CATEGORY_SEARCH_PERFIX . $category->getId() . 
						entry::CATEGORY_SEARCH_STATUS . $status;
			}
		}

		return implode(",", $categoryFullIdsToIds);
	}
	
	/**
	 * Convert the categories to categories ids
	 * 
	 * @param string $cats Categories full names
	 * @param string $statuses comma seperated
	 * @return string Categogories indexes ids
	 */
	public static function categoryIdsToAllSubCategoriesIdsParsed($cats, $statuses = null)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
			
		if($statuses == null || trim($statuses) == '')
			$statuses = CategoryEntryStatus::ACTIVE;
		
		$statuses = explode(',', trim($statuses));
		
		$categoryFullIdsToIds = array();
		foreach($cats as $catId)
		{				
			if(!$catId)
				continue;
							
			foreach ($statuses as $status)
			{
				//should return category itsef or sub categories
				$categoryFullIdsToIds[] = entry::CATEGORY_OR_PARENT_SEARCH_PERFIX . $catId . entry::CATEGORY_SEARCH_STATUS . $status;
			}
		}

		return implode(",", $categoryFullIdsToIds);
	}
	
	/**
	 * Convert the categories to categories ids
	 * to make search query shorter and to solve search problem when category tree is big.
 	 *
	 *	let's say entry belong to 2 categories with these full_ids
	 * 	111>222>333
	 *	111>444
	 * Old categories fields was: 
	 *	333,444
	 * 
	 * New categories filed:
	 * pc111,p111,pc222,p222,pc333,c333,pc444,c444
	 * 
	 * so why do we need pc111?
	 * If baseEntry->list with filter categoriesMatchOr= "111" you need to search for match pc111
	 * 
	 * so why do we need p111?
	 * If baseEntry->list with filter categoriesMatchOr= "111>" you need to search for match p111
	 * 	  
	 * @param string $cats Categories full names
	 * @param string $statuses comma seperated statuses.null = no status filtering (default criteria still applies)
	 * @return string Categogories indexes ids
	 */
	public static function categoryFullNamesToIdsParsed($cats, $commaSeparatedStatuses = null)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
		
		$commaSeparatedStatuses = trim( $commaSeparatedStatuses );
		if ( empty( $commaSeparatedStatuses ) )
		{
			$statuses = null;
		}
		else
		{
			$statuses = explode( ",", $commaSeparatedStatuses );
		}

		$categoryFullNamesToIds = array();
		foreach($cats as $cat)
		{
			if(substr($cat, -1) == '>')
			{
				//entries that doesn't belog directly to this category - but only to the sub categories.
				$categorySearchPrefix = entry::CATEGORY_PARENT_SEARCH_PERFIX;
				$cat = substr($cat, 0, strlen($cat) - 1);
			}
			else
			{
				//entries that belog directly to this category or to a sub categories.
				$categorySearchPrefix = entry::CATEGORY_OR_PARENT_SEARCH_PERFIX;
			} 
			
			$category = categoryPeer::getByFullNameExactMatch($cat);
			
			$categoryId = null;
			
			if(!$category)
				$categoryId = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
			else
				$categoryId = $category->getId();
			
			if ( $statuses )
			{
				foreach ($statuses as $status)
				{
					$categoryFullNamesToIds[] = $categorySearchPrefix . $categoryId .
							entry::CATEGORY_SEARCH_STATUS . $status;
				}
			}
			else
			{
				$categoryFullNamesToIds[] = $categoryId; // The cat. id AS-IS, no status filtering applied
			}
		}

		return implode(",", $categoryFullNamesToIds);
	}	

	/**
	 * Compose a category + status combined filter
	 * @param mixed $commaSeparatedCatIds One or more, comma separated, numeric (not names) category ids
	 * @param mixed|null $commaSeparatedStatuses One or more, comma separated, status ids. null = no status filtering (default criteria still applies)
	 * @return string Comma separated Sphinx IDs
	 */
	public static function categoryIdsToSphinxIds($commaSeparatedCatIds, $commaSeparatedStatuses = null)
	{
		$sphinxCategoryIdAndStatuses = array();

		if ($commaSeparatedCatIds === "")
		{
			$catIds = array();
		}
		else
		{
			$catIds = explode(",", $commaSeparatedCatIds);
		}

		kArray::trim($catIds);

		$commaSeparatedStatuses = trim( $commaSeparatedStatuses );
		if ( empty( $commaSeparatedStatuses ) )
		{
			$statuses = null;
		}
		else
		{
			$statuses = explode( ",", $commaSeparatedStatuses );
		}

		foreach ( $catIds as $catId )
		{
			if ( $statuses )
			{
				foreach ( $statuses as $status )
				{
					$sphinxCategoryIdAndStatuses[] = entry::CATEGORY_SEARCH_PERFIX . $catId . entry::CATEGORY_SEARCH_STATUS . $status;
				}
			}
			else
			{
				$sphinxCategoryIdAndStatuses[] = $catId; // The cat. id AS-IS, no status filtering applied
			}
		}

		return implode(",", $sphinxCategoryIdAndStatuses);
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

	protected function getRelativeTimeFields()
	{
		return array_merge(parent::getRelativeTimeFields(), self::$relative_time_fields);
	}
	
	public function transformFieldsToRelative()
	{
		foreach($this->getRelativeTimeFields() as $relativeFieldName)
		{
			$relativeFieldName = "_" . $relativeFieldName;
			if($this->is_set($relativeFieldName))
			{
				$value = $this->getByName($relativeFieldName);
				$value = kTime::getRelativeTime($value);
				$this->setByName($relativeFieldName, $value);
			}
		}
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
	
	public function setUserIdIn($v)
	{
		$this->set('_in_user_id', $v);
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
	
	public function setCategoriesIdsMatchAnd($v)
	{
		$this->set('_matchand_categories_ids', $v);
	}
	
	public function setCategoryAncestorId($v)
	{
		$this->set('_in_category_ancestor_id', $v);
	}
	
	public function setIsLive($v)
	{
		$this->set('_is_live', intval($v));
	}

	public function typeMatches(entry $entry)
	{		
		if ($this->get('_eq_type') && $entry->getType() != $this->get('_eq_type'))
		{
			return false;
		}
		
		if ($this->get('_in_type') && !in_array($entry->getType(), explode(',', $this->get('_in_type'))))
		{
			return false;
		}
		
		if ($this->get('_in_user_id') && !in_array($entry->getPuserId(), explode(',', $this->get('_in_user_id'))) )
		{
			return false;
		}
		
		return true;
	}
	
	public function setParentEntryIdEqual($v)
	{
		$this->set('_eq_parent_entry_id', ($v));
	}

}


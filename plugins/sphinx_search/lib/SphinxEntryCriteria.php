<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxEntryCriteria extends SphinxCriteria
{ 
	public static $sphinxFields = array(
		entryPeer::ID => 'int_entry_id',
		'entry.ENTRY_ID' => 'entry_id',
		entryPeer::NAME => 'name',
		entryPeer::TAGS => 'tags',
		entryPeer::CATEGORIES_IDS => 'categories',
		entryPeer::FLAVOR_PARAMS_IDS => 'flavor_params',
		entryPeer::SOURCE_LINK => 'source_link',
		entryPeer::KSHOW_ID => 'kshow_id',
		entryPeer::GROUP_ID => 'group_id',
		entryPeer::DESCRIPTION => 'description',
		entryPeer::ADMIN_TAGS => 'admin_tags',
		'entry.PLUGINS_DATA' => 'plugins_data',
		'entry.DYNAMIC_ATTRIBUTES' => 'dynamic_attributes',
		'entry.DURATION_TYPE' => 'duration_type',
		'entry.REFERENCE_ID' => 'reference_id',
		'entry.REPLACING_ENTRY_ID' => 'replacing_entry_id',
		'entry.REPLACED_ENTRY_ID' => 'replaced_entry_id',
		'entry.SEARCH_TEXT' => '(name,tags,description,entry_id,reference_id,roots,puser_id)',
		'entry.ROOTS' => 'roots',
		
		entryPeer::KUSER_ID => 'kuser_id',
		entryPeer::PUSER_ID => 'puser_id',
		entryPeer::STATUS => 'entry_status',
		entryPeer::TYPE => 'type',
		entryPeer::MEDIA_TYPE => 'media_type',
		entryPeer::VIEWS => 'views',
		entryPeer::PARTNER_ID => 'partner_id',
		entryPeer::MODERATION_STATUS => 'moderation_status',
		entryPeer::DISPLAY_IN_SEARCH => 'display_in_search',
		
		entryPeer::ACCESS_CONTROL_ID => 'access_control_id',
		entryPeer::MODERATION_COUNT => 'moderation_count',
		entryPeer::RANK => 'rank',
		entryPeer::TOTAL_RANK => 'total_rank',
		entryPeer::PLAYS => 'plays',
		entryPeer::LENGTH_IN_MSECS => 'length_in_msecs',
		'entry.PARTNER_SORT_VALUE' => 'partner_sort_value',
		'entry.REPLACEMENT_STATUS' => 'replacement_status',
		'entry.PARTNER_STATUS_VALUE' =>'partner_status_idx',
		
		entryPeer::CREATED_AT => 'created_at',
		entryPeer::UPDATED_AT => 'updated_at',
		entryPeer::MODIFIED_AT => 'modified_at',
		entryPeer::MEDIA_DATE => 'media_date',
		entryPeer::START_DATE => 'start_date',
		entryPeer::END_DATE => 'end_date',
		entryPeer::AVAILABLE_FROM => 'available_from',
		
		'entry.ENTITLED_KUSERS_PUBLISH' => 'entitled_kusers_publish',
		'entry.ENTITLED_KUSERS_EDIT' => 'entitled_kusers_edit',
		'entry.ENTITLED_KUSERS' => 'entitled_kusers',
		'entry.PRIVACY_BY_CONTEXTS' => 'privacy_by_contexts',
		'entry.CREATOR_KUSER_ID' => 'creator_kuser_id',
		'entry.CREATOR_PUSER_ID' => 'creator_puser_id',
	);
	
	public static $sphinxOrderFields = array(
		entryPeer::NAME => 'name',
		
		entryPeer::KUSER_ID => 'kuser_id',
		entryPeer::STATUS => 'entry_status',
		entryPeer::TYPE => 'type',
		entryPeer::MEDIA_TYPE => 'media_type',
		entryPeer::VIEWS => 'views',
		entryPeer::PARTNER_ID => 'partner_id',
		entryPeer::MODERATION_STATUS => 'moderation_status',
		entryPeer::DISPLAY_IN_SEARCH => 'display_in_search',
		entryPeer::LENGTH_IN_MSECS => 'length_in_msecs',
		entryPeer::ACCESS_CONTROL_ID => 'access_control_id',
		entryPeer::MODERATION_COUNT => 'moderation_count',
		entryPeer::RANK => 'rank',
		entryPeer::TOTAL_RANK => 'total_rank',
		entryPeer::PLAYS => 'plays',
		'entry.PARTNER_SORT_VALUE' => 'partner_sort_value',
		'entry.WEIGHT' => SphinxCriteria::WEIGHT,
		
		entryPeer::CREATED_AT => 'created_at',
		entryPeer::UPDATED_AT => 'updated_at',
		entryPeer::MODIFIED_AT => 'modified_at',
		entryPeer::MEDIA_DATE => 'media_date',
		entryPeer::START_DATE => 'start_date',
		entryPeer::END_DATE => 'end_date',
		entryPeer::AVAILABLE_FROM => 'available_from',
	);
	
	public static $sphinxTypes = array(
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'tags' => IIndexable::FIELD_TYPE_STRING,
		'categories' => IIndexable::FIELD_TYPE_STRING,
		'flavor_params' => IIndexable::FIELD_TYPE_STRING,
		'source_link' => IIndexable::FIELD_TYPE_STRING,
		'kshow_id' => IIndexable::FIELD_TYPE_STRING,
		'group_id' => IIndexable::FIELD_TYPE_STRING,
		'metadata' => IIndexable::FIELD_TYPE_STRING,
		'duration_type' => IIndexable::FIELD_TYPE_STRING,
		'reference_id' => IIndexable::FIELD_TYPE_STRING,
		'replacing_entry_id' => IIndexable::FIELD_TYPE_STRING,
		'replaced_entry_id' => IIndexable::FIELD_TYPE_STRING,
		'(name,tags,description,entry_id,reference_id,roots)' => IIndexable::FIELD_TYPE_STRING,
		'roots' => IIndexable::FIELD_TYPE_STRING,
		'description' => IIndexable::FIELD_TYPE_STRING,
		'admin_tags' => IIndexable::FIELD_TYPE_STRING,
		'plugins_data' => IIndexable::FIELD_TYPE_STRING,
		'dynamic_attributes' => IIndexable::FIELD_TYPE_JSON,
		'sphinx_match_optimizations'=> IIndexable::FIELD_TYPE_STRING,
		
		'int_entry_id' => IIndexable::FIELD_TYPE_INTEGER,
		'kuser_id' => IIndexable::FIELD_TYPE_STRING,
		'entry_status' => IIndexable::FIELD_TYPE_INTEGER,
		'type' => IIndexable::FIELD_TYPE_INTEGER,
		'media_type' => IIndexable::FIELD_TYPE_INTEGER,
		'views' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'moderation_status' => IIndexable::FIELD_TYPE_INTEGER,
		'display_in_search' => IIndexable::FIELD_TYPE_INTEGER,
		'length_in_msecs' => IIndexable::FIELD_TYPE_INTEGER,
		'access_control_id' => IIndexable::FIELD_TYPE_INTEGER,
		'moderation_count' => IIndexable::FIELD_TYPE_INTEGER,
		'rank' => IIndexable::FIELD_TYPE_INTEGER,
		'total_rank' => IIndexable::FIELD_TYPE_INTEGER,
		'plays' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
		'replacement_status' => IIndexable::FIELD_TYPE_INTEGER,
		
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'modified_at' => IIndexable::FIELD_TYPE_DATETIME,
		'media_date' => IIndexable::FIELD_TYPE_DATETIME,
		'start_date' => IIndexable::FIELD_TYPE_DATETIME,
		'end_date' => IIndexable::FIELD_TYPE_DATETIME,
		'available_from' => IIndexable::FIELD_TYPE_DATETIME,
	
		'entitled_kusers_publish' => IIndexable::FIELD_TYPE_STRING,
		'entitled_kusers_edit' => IIndexable::FIELD_TYPE_STRING,
		'entitled_kusers' => IIndexable::FIELD_TYPE_STRING,
		'privacy_by_contexts' => IIndexable::FIELD_TYPE_STRING,
		'creator_kuser_id' => IIndexable::FIELD_TYPE_STRING,
		'creator_puser_id' => IIndexable::FIELD_TYPE_STRING,
	);
	
	public static $sphinxOptimizationMap = array(
		// array(format, 'field1', 'field2',...)
		array(entry::PARTNER_STATUS_FORMAT, entryPeer::PARTNER_ID , entryPeer::STATUS),
		array("%s", entryPeer::ID),
	);

	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return entryPeer::getCriteriaFilter();
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(entryPeer::OM_CLASS);
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField()
	{
		return 'str_entry_id';
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getPropelIdField()
	 */
	protected function getPropelIdField()
	{
		return entryPeer::ID;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::doCountOnPeer()
	 */
	protected function doCountOnPeer(Criteria $c)
	{
		return entryPeer::doCount($c);
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter entryFilter */
		$categoriesAncestorParsed = null;
		$categories = $filter->get( "_in_category_ancestor_id");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesAncestorParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed ( $categories );

			if(!( $categoriesAncestorParsed !=='' || $categories ==''))
				$categoriesAncestorParsed = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		}
		$filter->unsetByName('_in_category_ancestor_id');
		
		$categories = $filter->get("_matchor_categories_ids");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			if(is_null($categoriesAncestorParsed))
				$categoriesParsed = $filter->categoryIdsToIdsParsed ( $categories );
			else 
				$categoriesParsed = $categoriesAncestorParsed;
			
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_matchor_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_matchor_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}else
		{
			$filter->set ( "_matchor_categories_ids", $categoriesAncestorParsed);
		}
		
		$categories = $filter->get( "_matchand_categories_ids");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryIdsToIdsParsed ( $categories );
			
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_matchand_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_matchand_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		
		$categoriesIds = $filter->get("_notcontains_categories_ids");
		if ($categoriesIds !== null)
		{
			$categoriesParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed($categoriesIds, CategoryEntryStatus::ACTIVE.','.CategoryEntryStatus::PENDING.','.CategoryEntryStatus::REJECTED);
			if ( $categoriesParsed !=='' || $categoriesIds =='')
				$filter->set ( "_notcontains_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_notcontains_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		
		$matchAndCats = $filter->get("_matchand_categories");
		if ($matchAndCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $matchAndCats );
			if ( $categoriesParsed !=='' || $matchAndCats =='')
				$filter->set ( "_matchand_categories_ids", $categoriesParsed );
			else
		  		$filter->set ( "_matchand_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_matchand_categories');
		}
		
		$matchOrCats = $filter->get("_matchor_categories");
		if ($matchOrCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $matchOrCats );
			if( $categoriesParsed !=='' || $matchOrCats=='')
				$filter->set("_matchor_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_matchor_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_matchor_categories');
		}
		
		$notContainsCats = $filter->get("_notcontains_categories");
		if ($notContainsCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ($notContainsCats, CategoryEntryStatus::ACTIVE.','.CategoryEntryStatus::PENDING.','.CategoryEntryStatus::REJECTED);
			if( $categoriesParsed !=='' || $notContainsCats=='')
				$filter->set("_notcontains_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_notcontains_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_notcontains_categories');
		}
		
		// match categories by full name		
		$CatFullNameIn = $filter->get("_in_categories_full_name");
		if ($CatFullNameIn !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $CatFullNameIn );
			if( $categoriesParsed !=='' || $CatFullNameIn=='')
				$filter->set("_matchor_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_matchor_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_in_categories_full_name');
		}		
		
		
		$matchOrRoots = array();
		if($filter->is_set('_eq_root_entry_id'))
		{
			$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX . ' ' . $filter->get('_eq_root_entry_id');
			$filter->unsetByName('_eq_root_entry_id');
		}
		
		if($filter->is_set('_in_root_entry_id'))
		{
			$roots = explode(baseObjectFilter::IN_SEPARATOR, $filter->get('_in_root_entry_id'));
			foreach($roots as $root)
				$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX . " $root";
				
			$filter->unsetByName('_in_root_entry_id');
		}
		
		if($filter->is_set('_is_root'))
		{
			if($filter->get('_is_root'))
				$filter->set('_notin_roots', entry::ROOTS_FIELD_ENTRY_PREFIX);
			else
				$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX;
				
			$filter->unsetByName('_is_root');
		}
		if(count($matchOrRoots))
			$filter->set('_matchand_roots', $matchOrRoots);
			
//		if ($filter->get("_matchor_duration_type") !== null)
//			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
		if($filter->get(baseObjectFilter::ORDER) === "recent" || $filter->get(baseObjectFilter::ORDER) === "-recent")
		{
			$filter->set("_lte_available_from", time());
			//$filter->set("_gteornull_end_date", time()); // schedule not finished
			$filter->set(baseObjectFilter::ORDER, "-available_from");
		}
			
		if($filter->get(baseObjectFilter::ORDER) === "+recent")
		{
			$filter->set(baseObjectFilter::ORDER, "+available_from");
		}
		
		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
			{
				$additionalConditions = $advancedSearch->getFreeTextConditions($filter->getPartnerSearchScope(), $freeTexts);
			}
			
			if(preg_match('/^"[^"]+"$/', $freeTexts))
			{
				$freeText = str_replace('"', '', $freeTexts);
				$freeText = SphinxUtils::escapeString($freeText);
				$freeText = "^$freeText$";
				$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
			}
			else
			{
				if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
				
					$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					foreach($freeTextsArr as $freeText)
					{
						$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
					}
				}
				else
				{
					$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					$freeTextsArr = array_unique($freeTextsArr);
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
				}
			}
			if(count($additionalConditions))
			{	
				$additionalConditions = array_unique($additionalConditions);
				$matches = reset($additionalConditions);
				if(count($additionalConditions) > 1)
					$matches = '( ' . implode(' ) | ( ', $additionalConditions) . ' )';
					
				$this->matchClause[] = $matches;
			}
		}
		$filter->unsetByName('_free_text');
		
		return parent::applyFilterFields($filter);
	}
	
	/**
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyPartnerScope(entryFilter $filter)
	{
		// depending on the partner_search_scope - alter the against_str 
		$partner_search_scope = $filter->getPartnerSearchScope();
		
		if ( baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE == $partner_search_scope )
		{
			// add nothing the the partner match
			
		}
		elseif ( $partner_search_scope == null  )
		{
			$this->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
		}
		else
		{
			if(count($partner_search_scope) == 1)
				$this->add(entryPeer::PARTNER_ID, $partner_search_scope, Criteria::EQUAL);
			else
				$this->add(entryPeer::PARTNER_ID, $partner_search_scope, Criteria::IN);
		}
	}
	
	/**
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilter(baseObjectFilter $filter)
	{
		$this->applyPartnerScope($filter);
		parent::applyFilter($filter);
	}

	public function hasSphinxFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
			
		return isset(self::$sphinxFields[$fieldName]);
	}
	
	public function getSphinxOrderFields()
	{			
		return self::$sphinxOrderFields;
	}
	
	public function getSphinxOptimizationMap() 
	{
		return self::$sphinxOptimizationMap;
	}
	
	public function getSphinxFieldName($fieldName)
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
	
	public function getSphinxFieldType($fieldName)
	{
		if(!isset(self::$sphinxTypes[$fieldName]))
			return null;
			
		return self::$sphinxTypes[$fieldName];
	}
	
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::isNullableField()
	 */
	public function isNullableField($fieldName)
	{
		return in_array($fieldName, entry::getIndexNullableFields());
	}
	
	public function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array(
			"name", 
			"description", 
			"tags", 
			"admin_tags", 
			"categories_ids", 
			"flavor_params_ids", 
			"duration_type", 
			"search_text",
			"reference_id",
			"replacing_entry_id",
			"replaced_entry_id",
			"roots",
			"plugins_data",
			"dynamic_attributes",
		));
	}

	public function getIdField()
	{
		return entryPeer::ID;
	}

	public function getSkipFields()
	{
		return array(entryPeer::ID);
	}
	
	public function getSphinxConditionsToKeep() {
		return array(entryPeer::PARTNER_ID);
	}
		
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
		
		$entryFields = entryPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $entryFields);
	}
	
	public function getFieldPrefix ($fieldName)
	{
		if($fieldName == 'roots')
			return entry::ROOTS_FIELD_PREFIX;
		
		if ($fieldName == 'categories')
			return entry::CATEGORIES_INDEXED_FIELD_PREFIX.kCurrentContext::getCurrentPartnerId();	
			
		return parent::getFieldPrefix($fieldName);
	}
}

<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCategoryCriteria extends SphinxCriteria
{
	public static $sphinxFields = array(
		'category.ID' => 'category_id',
		'category.CATEGORY_ID' => 'str_category_id',
		'category.PARENT_ID' => 'parent_id',
		'category.PARTNER_ID' => 'partner_id',
		'category.NAME' => 'name',
		'category.FULL_NAME' => 'full_name',
		'category.FULL_IDS' => 'full_ids',
		'category.DESCRIPTION' => 'description',
		'category.TAGS' => 'tags',
		'category.STATUS' => 'category_status',
		'category.KUSER_ID' => 'kuser_id',
		'category.DISPLAY_IN_SEARCH' => 'display_in_search',	
		'category.FREE_TEXT' => '(str_category_id,name,tags,description,reference_id)',
		'category.NAME_REFERNCE_ID' => '(name,reference_id)',
		'category.MEMBERS' => 'members',
		'plugins_data',
		'category.DEPTH' => 'depth',
		'category.REFERENCE_ID' => 'reference_id',
		'category.PRIVACY_CONTEXT' => 'privacy_context',
		'category.PRIVACY_CONTEXTS' => 'privacy_contexts',
		'category.MEMBERS_COUNT' => 'members_count',
		'category.PENDING_MEMBERS_COUNT' => 'pending_members_count',
		'category.ENTRIES_COUNT' => 'entries_count',
		'category.DIRECT_ENTRIES_COUNT' => 'direct_entries_count',
		'category.DIRECT_SUB_CATEGORIES_COUNT' => 'direct_sub_categories_count',
		'category.PRIVACY' => 'privacy',
		'category.INHERITANCE_TYPE' => 'inheritance_type',
		'category.USER_JOIN_POLICY' => 'user_join_policy',
		'category.DEFAULT_PERMISSION_LEVEL' => 'default_permission_level' ,
		'category.CONTRIBUTION_POLICY' => 'contribution_policy',
		'category.INHERITED_PARENT_ID' => 'inherited_parent_id',
		'category.CREATED_AT' => 'created_at',
		'category.UPDATED_AT' => 'updated_at',
		'category.DELETED_AT' => 'deleted_at',	
		'category.PARTNER_SORT_VALUE' => 'partner_sort_value',
	);
	
	public static $sphinxOrderFields = array(
		'category.NAME' => 'sort_name',
		'category.ID' => 'category_id',
		'category.PARTNER_ID' => 'partner_id',
		'category.STATUS' => 'category_status',
		'category.KUSER_ID' => 'kuser_id',
		'category.DISPLAY_IN_SEARCH' => 'display_in_search',	
		'category.DEPTH' => 'depth',
		'category.MEMBERS_COUNT' => 'members_count',
		'category.PENDING_MEMBERS_COUNT' => 'pending_members_count',
		'category.ENTRIES_COUNT' => 'entries_count',
		'category.DIRECT_ENTRIES_COUNT' => 'direct_entries_count',
		'category.DIRECT_SUB_CATEGORIES_COUNT' => 'direct_sub_categories_count',
		'category.CREATED_AT' => 'created_at',
		'category.UPDATED_AT' => 'updated_at',
		'category.PARTNER_SORT_VALUE' => 'partner_sort_value',
	);
	
	public static $sphinxTypes = array(
		'category_id' => IIndexable::FIELD_TYPE_INTEGER,
		'str_category_id' => IIndexable::FIELD_TYPE_STRING,
		'parent_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'full_name' => IIndexable::FIELD_TYPE_STRING,
		'full_ids' => IIndexable::FIELD_TYPE_STRING,
		'description' => IIndexable::FIELD_TYPE_STRING,
		'tags' => IIndexable::FIELD_TYPE_STRING,
		'category_status' => IIndexable::FIELD_TYPE_INTEGER,
		'kuser_id' => IIndexable::FIELD_TYPE_INTEGER,
		'display_in_search' => IIndexable::FIELD_TYPE_STRING,
		'members' => IIndexable::FIELD_TYPE_STRING,
		'plugins_data' => IIndexable::FIELD_TYPE_STRING,
		'depth' => IIndexable::FIELD_TYPE_INTEGER,
		'reference_id' => IIndexable::FIELD_TYPE_STRING,
		'privacy_context' => IIndexable::FIELD_TYPE_STRING,
		'privacy_contexts' => IIndexable::FIELD_TYPE_STRING,
		'privacy' => IIndexable::FIELD_TYPE_STRING,	
		'members_count' => IIndexable::FIELD_TYPE_INTEGER,
		'pending_members_count' => IIndexable::FIELD_TYPE_INTEGER,
		'entries_count' => IIndexable::FIELD_TYPE_INTEGER,
		'direct_entries_count' => IIndexable::FIELD_TYPE_INTEGER,
		'direct_sub_categories_count' => IIndexable::FIELD_TYPE_INTEGER,
		'inheritance_type' => IIndexable::FIELD_TYPE_INTEGER,
		'user_join_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'default_permission_level' => IIndexable::FIELD_TYPE_INTEGER,
		'contribution_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'inherited_parent_id' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'deleted_at' => IIndexable::FIELD_TYPE_DATETIME,
		'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
		'sphinx_match_optimizations'=> IIndexable::FIELD_TYPE_STRING,
	);
	
	public static $sphinxFieldsEscapeType = array(
		'category.FULL_NAME' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
		'category.FULL_IDS' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
	);
	
	public static $sphinxOptimizationMap = array(
			// array(format, 'field1', 'field2',...)
			array("%s", categoryPeer::ID),
	);

	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return categoryPeer::getCriteriaFilter();
	}
	
	protected function getEnableStar()
	{
		return true;
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(categoryPeer::getOMClass(false));
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField()
	{
		return 'id';
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getPropelIdField()
	 */
	protected function getPropelIdField()
	{
		return categoryPeer::ID;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::doCountOnPeer()
	 */
	protected function doCountOnPeer(Criteria $c)
	{
		return categoryPeer::doCount($c);
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{				
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$categories = $filter->get( "_matchor_likex_full_name");
		if ($categories !== null)
		{
			$categories = explode(',', $categories);
			$parsedCategories = array();
			foreach ($categories as $category)
			{
				if(trim($category) == '')
					continue;
				
				$parsedCategories[] = $category . '\\*';
			}
			
			$fullNameMatchOr = '';
			if(count($parsedCategories))
				$fullNameMatchOr = implode(',' , $parsedCategories);
			
			if($fullNameMatchOr != '')
				$filter->set ( "_matchor_full_name", $fullNameMatchOr);
		}
		$filter->unsetByName('_matchor_likex_full_name');
		
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
				$additionalConditions[] = "@(" . categoryFilter::FREE_TEXT_FIELDS . ") $freeText";
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
						$additionalConditions[] = "@(" . categoryFilter::FREE_TEXT_FIELDS . ") $freeText";
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
					$additionalConditions[] = "@(" . categoryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
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
		
		if($filter->get('_eq_privacy_context') && ($filter->get('_eq_privacy_context') == '*'))
		{
			$filter->set('_matchor_privacy_context', kEntitlementUtils::NOT_DEFAULT_CONTEXT);
			$filter->unsetByName('_eq_privacy_context');
		}
		
		if($filter->get('_eq_manager'))
		{
			$puserId = $filter->get('_eq_manager');
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			if($kuser)
			{
				$manager = category::getPermissionLevelName(CategoryKuserPermissionLevel::MANAGER);
				$this->matchClause[] = '(@(' . categoryFilter::MEMBERS . ') ' . $manager . '_' . $kuser->getid() . ')';
			}
		}
		$filter->unsetByName('_eq_manager');
		
		if($filter->get('_eq_member'))
		{
			$puserId = $filter->get('_eq_member');
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			if($kuser)
			{
				$manager = category::getPermissionLevelName(CategoryKuserPermissionLevel::MANAGER);
				$member = category::getPermissionLevelName(CategoryKuserPermissionLevel::MEMBER);
				$moderator = category::getPermissionLevelName(CategoryKuserPermissionLevel::MODERATOR);
				$contributor = category::getPermissionLevelName(CategoryKuserPermissionLevel::CONTRIBUTOR);
				$kuserId = $kuser->getid();
				$this->matchClause[] = '(@(' . categoryFilter::MEMBERS . ') ' . 
					"({$member}_{$kuserId} | {$moderator}_{$kuserId} | {$contributor}_{$kuserId} ) !({$manager}_{$kuserId}))";
			}
		}
		$filter->unsetByName('_eq_member');
		
		
		if($filter->get('_eq_full_name'))
		{
			$filter->set('_matchor_full_name', $filter->get('_eq_full_name') . category::FULL_NAME_EQUAL_MATCH_STRING);
		}
		$filter->unsetByName('_eq_full_name');
		
		if($filter->get('_in_full_name'))
		{
			$fullnames = explode(',', $filter->get('_in_full_name'));
			
			$fullnameIn = '';
			foreach($fullnames as $fullname)
				$fullnameIn .= $fullname . category::FULL_NAME_EQUAL_MATCH_STRING . ',';
			
			$filter->set('_matchor_full_name', $fullnameIn);
			$filter->unsetByName('_in_full_name');
		}

		$categories = $filter->get( "_in_ancestor_id");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed ( $categories );
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_likex_full_ids", $categoriesParsed);
			else
		  		$filter->set ( "_likex_full_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		$filter->unsetByName('_in_ancestor_id');
		
		if($filter->get('_likex_full_ids'))
		{
			$fullids = explode(',', $filter->get('_likex_full_ids'));
			
			$fullIdsIn = '';
			foreach($fullids as $fullid)
				$fullIdsIn .= $fullid . '\\*,';
			
			$filter->set('_matchor_full_ids', $fullIdsIn);
			$filter->unsetByName('_likex_full_ids');
		}
		
		if($filter->get('_eq_full_ids'))
		{
			$filter->set('_matchor_full_ids', $filter->get('_eq_full_ids') . category::FULL_IDS_EQUAL_MATCH_STRING);
		}
		$filter->unsetByName('_eq_full_ids');

		if($filter->get('_likex_name_or_reference_id'))
		{
			$names = $filter->get('_likex_name_or_reference_id');
			KalturaLog::debug("Attach free text [$names]");
			
			$additionalConditions = array();
			
			if(preg_match('/^"[^"]+"$/', $names))
			{
				$name = str_replace('"', '', $names);
				$name = SphinxUtils::escapeString($name);
				$name = "^$name$";
				$additionalConditions[] = "@(" . categoryFilter::NAME_REFERNCE_ID . ") $name\\\*";
			}
			else
			{
				if(strpos($names, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $names);
				
					$namesArr = explode(baseObjectFilter::IN_SEPARATOR, $names);
					foreach($namesArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($namesArr[$valIndex]);
						else
							$namesArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					foreach($namesArr as $name)
					{
						$additionalConditions[] = "@(" . categoryFilter::NAME_REFERNCE_ID . ") $name\\\*";
					}
				}
				else
				{
					$namesArr = explode(baseObjectFilter::AND_SEPARATOR, $names);
					foreach($namesArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($namesArr[$valIndex]);
						else
							$namesArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					$namesArr = array_unique($namesArr);
					$nameExpr = implode(baseObjectFilter::AND_SEPARATOR, $namesArr);
					$additionalConditions[] = "@(" . categoryFilter::NAME_REFERNCE_ID . ") $nameExpr\\\*";
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
		$filter->unsetByName('_likex_name_or_reference_id');
		
		
		if($filter->get('_eq_privacy')) {
			$filter->set('_eq_privacy', $filter->get('_eq_privacy') . "P" . $partnerId);
		}
		
		if($filter->get('_in_privacy'))  {
			$privacyIn = explode(',', $filter->get('_in_privacy'));
				
			$newPrivacyIn = array();
			foreach($privacyIn as $privacy)
				$newPrivacyIn[] =  $privacy . "P" . $partnerId;
				
			$filter->set('_in_privacy', implode(",", $newPrivacyIn));
		}
		
		return parent::applyFilterFields($filter);
	}

	public function hasSphinxFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "category.$fieldName";
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
			$fieldName = "category.$fieldName";
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
	
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "category.$fieldName";
		}
		
		if(!isset(self::$sphinxFieldsEscapeType[$fieldName]))
			return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
			
		return self::$sphinxFieldsEscapeType[$fieldName];
	}
	
	public function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array(
			"name", 
			"full_name",
			"full_ids",
			"free_text",
			"description", 
			"tags", 
			"members",
			"privacy_context",
		));
	}

	public function getIdField()
	{
		return categoryPeer::ID;
	}

	public function getSkipFields()
	{
		return array(categoryPeer::ID);
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "category.$fieldName";
		}

		$categoryFields = categoryPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $categoryFields);
	}
	
	public function getTranslateIndexId($id)
	{
		return $id;
	}
}

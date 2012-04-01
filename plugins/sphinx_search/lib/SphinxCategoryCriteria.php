<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCategoryCriteria extends SphinxCriteria
{
	public static $sphinxFields = array(
		'category.ID' => 'category_id',
		'category.PARTNER_ID' => 'partner_id',
		'category.NAME' => 'name',
		'category.FULL_NAME' => 'full_name',
		'category.DESCRIPTION' => 'description',
		'category.TAGS' => 'tags',
		'category.STATUS' => 'category_status',
		'category.KUSER_ID' => 'kuser_id',
		'category.DISPLAY_IN_SEARCH' => 'display_in_search',	
		'category.FREE_TEXT' => '(name,tags,description)',
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
		'category.PRIVACY' => 'privacy',
		'category.INHERITANCE_TYPE' => 'inheritance_type',
		'category.USER_JOIN_POLICY' => 'user_join_policy',
		'category.DEFAULT_PERMISSION_LEVEL' => 'default_permission_level' ,
		'category.CONTRIBUTION_POLICY' => 'contribution_policy',
		'category.INHERITED_PARENT_ID' => 'inherited_parent_id',
		'category.CREATED_AT' => 'created_at',
		'category.UPDATED_AT' => 'updated_at',
		'category.DELETED_AT' => 'deleted_at',	
	);
	
	public static $sphinxOrderFields = array(
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
		'category.CREATED_AT' => 'created_at',
		'category.UPDATED_AT' => 'updated_at',
	);
	
	public static $sphinxTypes = array(
		'category_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'full_name' => IIndexable::FIELD_TYPE_STRING,
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
		'inheritance_type' => IIndexable::FIELD_TYPE_INTEGER,
		'user_join_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'default_permission_level' => IIndexable::FIELD_TYPE_INTEGER,
		'contribution_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'inherited_parent_id' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'deleted_at' => IIndexable::FIELD_TYPE_DATETIME
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
		$matchAndCats = $filter->get("in_members");
		if ($matchAndCats !== null)
		{
			//TODO - implement pusers to kusers
			$filter->unsetByName('in_members');
		}
		
		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
			{
				$additionalConditions = $advancedSearch->getFreeTextConditions($freeTexts);
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
	
	public function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array(
			"name", 
			"full_name",
			"description", 
			"tags", 
			"members"
		));
	}

	public function getIdField()
	{
		return categoryPeer::ID;
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

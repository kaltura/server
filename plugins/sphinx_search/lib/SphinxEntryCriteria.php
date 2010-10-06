<?php

class SphinxEntryCriteria extends KalturaCriteria
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
		'entry.SEARCH_TEXT' => '(name,tags,description)',
		
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
		'(name,tags,description)' => 'string',
		
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
	
	/**
	 * Field keys to be removed from the criteria after all filters applied 
	 * @var array
	 */
	protected $keyToRemove = array();
		
	/**
	 * Sphinx where clauses
	 * @var array
	 */
	protected $whereClause = array();
	
	/**
	 * Sphinx textual match clauses
	 * @var array
	 */
	protected $matchClause = array();
	
	/**
	 * Array of specific ids that could be returned
	 * Used for _in_id and _eq_id filter fields 
	 * The form is array[$operator] = array($entryId1 => $entryCrc1, $entryId2 => $entryCrc2)
	 * @var array
	 */
	protected $entryIds = array();
		
	/**
	 * The count of total returned items
	 * @var int
	 */
	private static $recordsCount = 0;
	
	/**
	 * Counts how many criterions couldn't be handled
	 * @var int
	 */
	private $criteriasLeft;
	
	/**
	 * @return int $recordsCount
	 */
	public function getRecordsCount() {
		return self::$recordsCount;
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria#applyFilters()
	 */
	public function applyFilters()
	{
		$this->criteriasLeft = 0;
		
		KalturaLog::debug("Applies " . count($this->filters) . " filters");
		
		foreach($this->filters as $index => $filter)
		{
			KalturaLog::debug("Applies filter $index");
			$this->applyFilter($filter);
		}
		
		// attach all default criteria from peer
		entryPeer::getCriteriaFilter()->applyFilter($this);
		
		// go over all criterions and try to move them to the sphinx
		foreach($this->getMap() as $field => $criterion)
		{
			if(!($criterion instanceof SphinxEntryCriterion))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] is not sphinx criteria");
				$this->criteriasLeft++;
				continue;
			}
			
			if($criterion->apply($this->whereClause, $this->matchClause))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] attached");
				$this->keyToRemove[] = $field;
			}
			else
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] failed");
				$this->criteriasLeft++;
			}
		}
		
		KalturaLog::debug("Applied " . count($this->matchClause) . " matches, " . count($this->whereClause) . " clauses, " . count($this->keyToRemove) . " keys removed, $this->criteriasLeft keys left");
		
		if(count($this->matchClause))
		{
			$matches = implode(' & ', $this->matchClause);
			$this->whereClause[] = "MATCH('$matches')";
		}
		
		$wheres = '';
		if(count($this->whereClause))
			$wheres = 'WHERE ' . implode(' AND ', $this->whereClause);

		$orderBy = '';
		$orderByColumns = $this->getOrderByColumns();
		$orderByColumns = array_unique($orderByColumns);
		
		$setLimit = true;
		if(count($orderByColumns))
		{
			$replace = self::$sphinxOrderFields;
			$search = array_keys($replace);
			
			$orders = array();
			foreach($orderByColumns as $orderByColumn)
			{
				$arr = explode(' ', $orderByColumn);
				$orderField = $arr[0];
				
				if(isset($replace[$orderField]))
				{
					KalturaLog::debug("Add sort field[$orderField] copy from [$orderByColumn]");
					$orders[] = str_replace($search, $replace, $orderByColumn);
				}
				else
				{
					KalturaLog::debug("Skip sort field[$orderField] from [$orderByColumn] limit won't be used in sphinx query");
					$setLimit = false;
				}
			}
				
			if(count($orders))
				$orderBy = 'ORDER BY ' . implode(',', $orders);
		}
			
		$index = kSphinxSearchManager::SPHINX_INDEX_NAME;
		$maxMatches = kSphinxSearchManager::SPHINX_MAX_RECORDS;
		$limit = $maxMatches;
		
		if($this->criteriasLeft)
			$setLimit = false;
			
		if($setLimit && $this->getLimit())
		{
			$limit = $this->getLimit();
			if($this->getOffset())
				$limit = $this->getOffset() . ", $limit";
		}
		
		$sql = "SELECT str_entry_id FROM $index $wheres $orderBy LIMIT $limit OPTION max_matches=$maxMatches";
		
		//debug query
		//echo $sql."\n"; die;
		$pdo = DbManager::getSphinxConnection();
		$stmt = $pdo->query($sql);
		if(!$stmt)
		{
			KalturaLog::err("Invalid sphinx query [$sql]");
			return;
		}
		
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 2);
		
		if(count($this->entryIds))
		{
			foreach($this->entryIds as $comparison => $entryIds)
			{
				// keeps only ids that appears in both arrays
				if($comparison == Criteria::IN)
				{
					$ids = array_intersect($ids, array_keys($entryIds));
				}
				
				// removes ids that appears in the comparison array
				if($comparison == Criteria::NOT_IN)
				{
					$ids = array_diff($ids, array_keys($entryIds));
				}
			}
		}
		KalturaLog::debug("Found " . count($ids) . " ids");
		
		foreach($this->keyToRemove as $key)
		{
			KalturaLog::debug("Removing key [$key] from criteria");
			$this->remove($key);
		}
		
		$this->addAnd(entryPeer::ID, $ids, Criteria::IN);
		
		self::$recordsCount = 0;
		
		if($setLimit)
		{
			$this->setOffset(0);
			
			$sql = "show meta";
			$stmt = $pdo->query($sql);
			$meta = $stmt->fetchAll(PDO::FETCH_NAMED);
			if(count($meta))
			{
				foreach($meta as $metaItem)
				{
					KalturaLog::debug("Sphinx query " . $metaItem['Variable_name'] . ': ' . $metaItem['Value']);
					if($metaItem['Variable_name'] == 'total_found')
						self::$recordsCount = (int)$metaItem['Value'];
				}
			}
		}
		else
		{
			$c = clone $this;
			$c->setLimit(null);
			$c->setOffset(null);
			self::$recordsCount = entryPeer::doCount($c);
		}
	}
	
	/**
	 * Applies all filter fields and unset the handled fields
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilterFields(entryFilter $filter)
	{
		if ($filter->get("_matchand_categories") !== null)
		{
			$filter->set("_matchand_categories_ids", $filter->categoryNamesToIds($filter->get("_matchand_categories")));
			$filter->unsetByName('_matchand_categories');
		}
			
		if ($filter->get("_matchor_categories") !== null)
		{
			$filter->set("_matchor_categories_ids", $filter->categoryNamesToIds($filter->get("_matchor_categories")));
			$filter->unsetByName('_matchor_categories');
		}
			
//		if ($filter->get("_matchor_duration_type") !== null)
//			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
		if ($filter->get(baseObjectFilter::ORDER) === "recent")
		{
			$filter->set("_lte_available_from", time());
			$filter->set("_gteornull_end_date", time()); // schedule not finished
			$filter->set(baseObjectFilter::ORDER, "-available_from");
		}
		
		if($filter->get('_free_text'))
		{
			KalturaLog::debug('No advanced filter defined');
		
			$freeTexts = $filter->get('_free_text');
			
			$additionalConditions = array();
			if(preg_match('/^"[^"]+"$/', $freeTexts))
			{
				$freeText = str_replace('"', '', $freeTexts);
				$freeText = SphinxUtils::escapeString($freeText);
				$freeText = "^$freeText$";
				$additionalConditions[] = "@(name,tags,description) $freeText";
			}
			else
			{
				if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
				
					$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					foreach($freeTextsArr as $freeText)
					{
						$additionalConditions[] = "(@(name,tags,description) $freeText)";
					}
				}
				else
				{
					$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "(@(name,tags,description) $freeTextExpr)";
				}
			}
			if(count($additionalConditions))
				$this->matchClause[] = implode(' | ', $additionalConditions);
		}
		$filter->unsetByName('_free_text');
	
		foreach($filter->fields as $field => $val)
		{	
			if(is_null($val) || !strlen($val)) 
			{
//				KalturaLog::debug("Skip field[$field] value is null");
				continue;
			}
			
			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			if(count($fieldParts) != 3)
			{
				KalturaLog::debug("Skip field[$field] has [" . count($fieldParts) . "] parts");
				continue;
			}
			
			list($prefix, $operator, $fieldName) = $fieldParts;
			
			$fieldNamesArr = explode(baseObjectFilter::OR_SEPARATOR, $fieldName);
			if(count($fieldNamesArr) > 1)
			{
				$sphinxFieldNames = array();
				foreach($fieldNamesArr as $fieldName)
				{
					$sphinxField = self::getSphinxFieldName($fieldName);
					$type = self::getSphinxFieldType($sphinxField);
					$sphinxFieldNames[] = $sphinxField;
				}
				$sphinxField = '(' . implode(',', $sphinxFieldNames) . ')';
				$vals = array_unique(explode(baseObjectFilter::OR_SEPARATOR, $val));
				$val = implode(' ', $vals);
			}
			elseif(!self::hasMatchableField($fieldName))
			{
				KalturaLog::debug("Skip field[$field] has no matchable for name[$fieldName]");
				continue;
			}
			else
			{
				$sphinxField = self::getSphinxFieldName($fieldName);
				$type = self::getSphinxFieldType($sphinxField);
			}
			$valStr = print_r($val, true);
			
			KalturaLog::debug("Attach field[$fieldName] as sphinx field[$sphinxField] of type [$type] and comparison[$operator] for value[$valStr]");
			switch($operator)
			{
				case baseObjectFilter::MULTI_LIKE_OR:
				case baseObjectFilter::MATCH_OR:
					$vals = explode(',', $val);
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
					
					if(count($vals))
					{
						$val = implode(' | ', $vals);
						$this->matchClause[] = "@$sphinxField $val";
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::NOT_IN:
					$vals = array();
					if(is_string($val))
						$vals = explode(',', $val);
					elseif(is_array($val))
						$vals = $val;
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$val = '!' . implode(' & !', $vals);
						$this->matchClause[] = "@$sphinxField $val";
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::IN:
					$vals = array();
					if(is_string($val))
						$vals = explode(',', $val);
					elseif(is_array($val))
						$vals = $val;
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$val = '(^' . implode('$ | ^', $vals) . '$)';
						$this->matchClause[] = "@$sphinxField $val";
						$filter->unsetByName($field);
					}
					break;
				
				
				case baseObjectFilter::EQ:
					if(is_numeric($val) || strlen($val) > 1)
					{
						$val = SphinxUtils::escapeString($val);
						$this->matchClause[] = "@$sphinxField ^$val$";
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::MULTI_LIKE_AND:
				case baseObjectFilter::MATCH_AND:
				case baseObjectFilter::LIKE:
					$vals = explode(' ', $val);
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					if(count($vals))
					{
						$val = implode(' & ', $vals);
						$this->matchClause[] = "@$sphinxField $val";
						$filter->unsetByName($field);
					}
					break;
					
				default:
					KalturaLog::debug("Skip field[$field] has no opertaor[$operator]");
			}
		}
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
			// add nothing the the match
		}
		elseif ( $partner_search_scope == null  )
		{
			$this->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
		}
		else
		{
			$this->add(entryPeer::PARTNER_ID, $partner_search_scope, Criteria::IN);
		}
	}
	
	/**
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilter(entryFilter $filter)
	{
		$advancedSearch = $filter->getAdvancedSearch();
		if(is_object($advancedSearch) && $advancedSearch instanceof AdvancedSearchFilter)
		{
			KalturaLog::debug('Apply advanced filter [' . get_class($advancedSearch) . ']');
			$advancedSearch->apply($filter, $this, $this->matchClause, $this->whereClause);
		}
		
		$this->applyFilterFields($filter);
		$this->applyPartnerScope($filter);
		
		// attach all unhandled fields
		$filter->attachToFinalCriteria($this);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#getNewCriterion()
	 */
	public function getNewCriterion($column, $value, $comparison = null)
	{
		return new SphinxEntryCriterion($this, $column, $value, $comparison);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#add()
	 */
	public function add($p1, $value = null, $comparison = null)
	{
		if ($p1 instanceof Criterion)
		{
			$oc = $this->getCriterion($p1->getColumn());
			if(!is_null($oc) && $oc->getValue() == $p1->getValue() && $oc->getComparison() != $p1->getComparison())
				return $this;
				
			return parent::add($p1);
		}
		
		$nc = new SphinxEntryCriterion($this, $p1, $value, $comparison);
		return parent::add($nc);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#addAnd()
	 */
	public function addAnd($p1, $p2 = null, $p3 = null)
	{
		if (is_null($p3)) 
			return parent::addAnd($p1, $p2, $p3);
			
		// addAnd(column, value, comparison)
		$nc = new SphinxEntryCriterion($this, $p1, $p2, $p3);
		$oc = $this->getCriterion($p1);
		
		if ( !is_null($oc) )
		{
			// no need to add again
			if($oc->getValue() != $p2 || $oc->getComparison() != $p3)
				$oc->addAnd($nc);
				
			return $this;
		}
			
		return $this->add($nc);
	}
	
	public function setEntryIds($comparison, $entryIds)
	{
		$this->entryIds[$comparison] = $entryIds;
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
	
	public static function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array("name", "description", "tags", "admin_tags", "categories_ids", "flavor_params_ids", "duration_type", "search_text"));
	}
}
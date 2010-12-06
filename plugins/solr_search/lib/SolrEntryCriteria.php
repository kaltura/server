<?php

class SolrEntryCriteria extends KalturaCriteria
{
	// allow no more than 100 values in IN and NOT_IN clause
	const MAX_IN_VALUES = 100;
	
	const SOLR_MAX_RECORDS = 10000;
	
	public static $solrFields = array(
		entryPeer::ID => 'id',
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
	
	public static $solrOrderFields = array(
		entryPeer::NAME => 'name',
		
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
	
	public static $solrTypes = array(
		'id' => 'string',
		'name' => 'string',
		'tags' => 'string',
		'categories' => 'string',
		'flavor_params' => 'string',
		'source_link' => 'string',
		'kshow_id' => 'string',
		'group_id' => 'string',
		'metadata' => 'string',
		
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
		
		'created_at' => 'date',
		'updated_at' => 'date',
		'modified_at' => 'date',
		'media_date' => 'date',
		'start_date' => 'date',
		'end_date' => 'date',
		'available_from' => 'date',
	);
	
	
	/**
	 * Field keys to be removed from the criteria after all filters applied 
	 * @var array
	 */
	protected $keyToRemove = array();
		
	/**
	 * Solr where clauses
	 * @var array
	 */
	protected $whereClause = array();
	
	/**
	 * Counts how many criterions couldn't be handled
	 * @var int
	 */
	private $criteriasLeft;
	
	/* (non-PHPdoc)
	 * @see SolrCriteria#applyFilters()
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
		
		// go over all criterions and try to move them to the solr
		foreach($this->getMap() as $field => $criterion)
		{
			if(!($criterion instanceof SolrEntryCriterion))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] is not solr criteria");
				$this->criteriasLeft++;
				continue;
			}
			
			if($criterion->apply($this->whereClause))
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
		
		KalturaLog::debug("Applied " . count($this->whereClause) . " matches, " . count($this->whereClause) . " clauses, " . count($this->keyToRemove) . " keys removed, $this->criteriasLeft keys left");
		
		$wheres = '';
		if(count($this->whereClause))
		{
			$wheres = "";
			foreach($this->whereClause as $where)
			{
				$c = @$where[0];
				$wheres .= (($c == "-" || $c == "+") ? " " : " +").$where;
			}
		}

		$orderBy = '';
		$orderByColumns = $this->getOrderByColumns();
		$orderByColumns = array_unique($orderByColumns);
		
		$setLimit = true;
		if(count($orderByColumns))
		{
			$replace = self::$solrOrderFields;
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
					KalturaLog::debug("Skip sort field[$orderField] from [$orderByColumn] limit won't be used in solr query");
					$setLimit = false;
				}
			}
				
			if(count($orders))
				$orderBy = 'sort=' . implode(',', $orders);
		}
			
		$limit = self::SOLR_MAX_RECORDS;
		
		if($this->criteriasLeft)
			$setLimit = false;
			
		$offset = 0;
			
		if($setLimit && $this->getLimit())
		{
			$limit = $this->getLimit();
			if($this->getOffset())
				$offset = $this->getOffset();
		}
		
		$query = "$wheres $orderBy";
		
		echo $query."\n";
		die;
		
		$solr = kSolrSearchManager::createSolrService();
		$response = $solr->search($query, $offset, $limit);
		
		if(!$response)
		{
			KalturaLog::err("Invalid solr query [$sql]");
			return;
		}
		
		$response = json_decode($response);
		
		$response = $response['response'];
		$docs = $response['docs'];
		$ids = $array();
		foreach($docs as $doc)
			$ids[] = $doc["id"];
	
		KalturaLog::debug("Found " . count($ids) . " ids");
		
		foreach($this->keyToRemove as $key)
		{
			KalturaLog::debug("Removing key [$key] from criteria");
			$this->remove($key);
		}
		
		$this->addAnd(entryPeer::ID, $ids, Criteria::IN);
		
		$this->recordsCount = 0;
		
		if($setLimit)
		{
			$this->recordsCount = $response['numFound'];
		}
		else
		{
			$c = clone $this;
			$c->setLimit(null);
			$c->setOffset(null);
			$this->recordsCount = entryPeer::doCount($c);
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
			
		if ($filter->get("_matchor_duration_type") !== null)
			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
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
				$additionalConditions[] = $freeText; // fixme - only name,tags,description ?
			}
			else
			{
				if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
				
					$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
							
					foreach($freeTextsArr as $freeText)
					{
						$additionalConditions[] = $freeText; // fixme - only name,tags,description ?
					}
				}
				else
				{
					$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
							
					$freeTextExpr = implode(" +", $freeTextsArr);
					$additionalConditions[] = $freeTextExpr; // fixme - only name,tags,description ?
				}
			}
			if(count($additionalConditions))
				$this->whereClause[] = '(' . implode(' ', $additionalConditions). ')';
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
				$solrFieldNames = array();
				foreach($fieldNamesArr as $fieldName)
				{
					$solrField = self::getSolrFieldName($fieldName);
					$type = self::getSolrFieldType($solrField);
					$solrFieldNames[] = $solrField;
				}
				$solrField = '(' . implode(',', $solrFieldNames) . ')';
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
				$solrField = self::getSolrFieldName($fieldName);
				$type = self::getSolrFieldType($solrField);
			}
			$valStr = print_r($val, true);
			
			KalturaLog::debug("Attach field[$fieldName] as solr field[$solrField] of type [$type] and comparison[$operator] for value[$valStr]");
			switch($operator)
			{
				case baseObjectFilter::MULTI_LIKE_OR:
				case baseObjectFilter::MATCH_OR:
					$vals = explode(',', $val);
					foreach($vals as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
					
					if(count($vals))
					{
						$this->whereClause[] = "$solrField:(" . implode(" ", $vals) . ")";
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
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$this->whereClause[] = "-$solrField:(" . implode(" ", $vals) . ")";
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
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$this->whereClause[] = "$solrField:(" . implode(" ", $vals) . ")";
						$filter->unsetByName($field);
					}
					break;
				
				
				case baseObjectFilter::EQ:
					if(is_numeric($val) || strlen($val) > 1)
					{
						$this->whereClause[] = "$solrField:$val"; // fixme - find exact match
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::MULTI_LIKE_AND:
				case baseObjectFilter::MATCH_AND:
				case baseObjectFilter::LIKE:
					$vals = explode(',', $val);
					foreach($vals as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
											
					if(count($vals))
					{
						$this->whereClause[] = "$solrField:(+".implode(" +", $vals).")";
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
			$advancedSearch->apply($filter, $this, $this->whereClause, $this->whereClause);
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
		return new SolrEntryCriterion($this, $column, $value, $comparison);
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
		
		$nc = new SolrEntryCriterion($this, $p1, $value, $comparison);
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
		$nc = new SolrEntryCriterion($this, $p1, $p2, $p3);
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
	
	public static function getSolrFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
			
		if(!isset(self::$solrFields[$fieldName]))
			return $fieldName;
			
		return self::$solrFields[$fieldName];
	}
	
	public static function getSolrFieldType($fieldName)
	{
		if(!isset(self::$solrTypes[$fieldName]))
			return null;
			
		return self::$solrTypes[$fieldName];
	}
	
	public static function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array("name", "description", "tags", "admin_tags", "categories_ids", "flavor_params_ids"));
	}	
}
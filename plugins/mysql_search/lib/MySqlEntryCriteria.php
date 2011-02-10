<?php

class MySqlEntryCriteria extends MySqlCriteria
{
	public static $mySqlFields = array(
		'plugins_data',
		'entry.DURATION_TYPE' => 'duration_type',
		'entry.SEARCH_TEXT' => 'search_text',
	);
	
	public static $mySqlOrderFields = array(
	);
	
	public static $mySqlTypes = array(
		'metadata' => 'string',
		'duration_type' => 'string',
		'search_text' => 'string',
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
	protected function getMySqlIndexName()
	{
		return kMySqlSearchManager::getMySqlIndexName(entryPeer::TABLE_NAME);;
	}
	
	protected function executeMySql($index, $wheres, $orderBy, $limit, $setLimit)
	{
		$sql = "SELECT entry_id FROM $index $wheres $orderBy LIMIT $limit";
		
		// TODO - add DB config plugin
		$pdo = DbManager::getConnection(MySqlSearchPlugin::getDbConnectionName());
		$stmt = $pdo->query($sql);
		if(!$stmt)
		{
			KalturaLog::err("Invalid mySql query [$sql]");
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
		
		$this->recordsCount = 0;
		
		if(!$this->doCount)
			return;
			
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
					KalturaLog::debug("MySql query " . $metaItem['Variable_name'] . ': ' . $metaItem['Value']);
					if($metaItem['Variable_name'] == 'total_found')
						$this->recordsCount = (int)$metaItem['Value'];
				}
			}
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
	protected function applyFilterFields(baseObjectFilter $filter)
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
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = $valValue;
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
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = $valValue;
					}
							
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
				}
			}
			if(count($additionalConditions))
				$this->matchClause[] = implode(' | ', $additionalConditions);
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
	protected function applyFilter(baseObjectFilter $filter)
	{
		$this->applyPartnerScope($filter);
		parent::applyFilter($filter);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#getNewCriterion()
	 */
	public function getNewCriterion($column, $value, $comparison = null)
	{
		return new MySqlCriterion('MySqlEntryCriteria', $this, $column, $value, $comparison);
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
		
		$nc = new MySqlCriterion('MySqlEntryCriteria', $this, $p1, $value, $comparison);
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
		$nc = new MySqlCriterion('MySqlEntryCriteria', $this, $p1, $p2, $p3);
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
	
	public function hasMySqlFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
			
		return isset(self::$mySqlFields[$fieldName]);
	}
	
	public function getMySqlOrderFields()
	{
		return self::$mySqlOrderFields;
	}
	
	public function getMySqlFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
			
		if(!isset(self::$mySqlFields[$fieldName]))
			return $fieldName;
			
		return self::$mySqlFields[$fieldName];
	}
	
	public function getMySqlFieldType($fieldName)
	{
		if(!isset(self::$mySqlTypes[$fieldName]))
			return null;
			
		return self::$mySqlTypes[$fieldName];
	}
	
	public function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array("name", "description", "tags", "admin_tags", "categories_ids", "flavor_params_ids", "duration_type", "search_text"));
	}
}

<?php

abstract class MySqlCriteria extends KalturaCriteria
{
	protected $removedKeys = array();
		
	/**
	 * @return criteriaFilter
	 */
	abstract protected function getDefaultCriteriaFilter();
	
	/**
	 * @param string $field
	 * @return string the name of the field in the search table
	 */
	abstract protected function getSearchFieldName($field);
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	abstract public function hasMatchableField($fieldName);
	
	/**
	 * @return IMySqlSearchPeer
	 */
	abstract protected function getSearchPeer();
	
	/* (non-PHPdoc)
	 * @see MySqlCriteria#applyFilters()
	 */
	public function applyFilters()
	{
		$this->criteriasLeft = 0;
		
		KalturaLog::debug("Applies " . count($this->filters) . " filters");
		
		foreach($this->filters as $index => $filter)
		{
			KalturaLog::debug("Applies filter $index");
			$this->applyFilter(clone $filter);
		}
		
		// attach all default criteria from peer
		$this->getDefaultCriteriaFilter()->applyFilter($this);
		
		$peer = $this->getSearchPeer();
		
		// go over all criterions and try to move them to the MySql
		foreach($this->getMap() as $field => $criterion)
		{
			try
			{
				$peer->translateFieldName($field);
				$this->remove($field);
				$this->removedKeys[$field] = $criterion;
				KalturaLog::debug("Criterion on [$field] moved to the search object");
			}
			catch(PropelException $pe)
			{
				KalturaLog::debug("Criterion on [$field] could not be moved to the search object");
			}
		}
		
		$this->clearSelectColumns();
		$this->addSelectColumn($peer->getSearchPrimaryKeyField());
		
		$stmt = $peer->doSelectStmt($this);
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		KalturaLog::debug("Search returned [" . count($ids) . "] ids");
		
		if(count($this->removedKeys))
		{
			foreach($this->removedKeys as $field => $criterion)
				$this->add($field, $criterion);
				
			$countCriteria = clone $this;
			$countCriteria->setLimit(null);
			$this->recordsCount = $peer->doCountOnSourceTable($countCriteria);
		}
		else
		{
			$countCriteria = clone $this;
			$countCriteria->setLimit(null);
			$this->recordsCount = $peer->doCount($countCriteria);
		}
		
		$this->add($peer->getPrimaryKeyField(), $ids);
		$this->clearSelectColumns();
	}
	
	/**
	 * Applies all filter fields and unset the handled fields
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
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
			
			$mySqlField = $fieldName;
			$mySqlCriterionField = $fieldName;
			
			$fieldNamesArr = explode(baseObjectFilter::OR_SEPARATOR, $fieldName);
			if(count($fieldNamesArr) > 1)
			{
				$mySqlFieldNames = array();
				foreach($fieldNamesArr as $fieldName)
				{
					$mySqlField = $this->getSearchFieldName($fieldName);
					$mySqlFieldNames[] = $mySqlField;
				}
				$mySqlCriterionField = $mySqlField;
				$mySqlField = implode(',', $mySqlFieldNames);
				$vals = is_array($val) ? $val : array_unique(explode(baseObjectFilter::OR_SEPARATOR, $val));
				$val = implode(' ', $vals);
			}
			elseif(!$this->hasMatchableField($fieldName))
			{
				KalturaLog::debug("Skip field[$field] has no matchable for name[$fieldName]");
				continue;
			}
			else
			{
				$mySqlField = $this->getMySqlFieldName($fieldName);
				$mySqlCriterionField = $mySqlField;
			}
			$valStr = print_r($val, true);
			
			KalturaLog::debug("Attach field[$fieldName] as search field[$mySqlField] and comparison[$operator] for value[$valStr]");
			switch($operator)
			{
				case baseObjectFilter::MULTI_LIKE_OR:
				case baseObjectFilter::MATCH_OR:
					$vals = is_array($val) ? $val : explode(',', $val);
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue))
							$vals[$valIndex] = '"' . $valValue . '"';
						else
							$vals[$valIndex] = $valValue;
					}
					
					if(count($vals))
					{
						$val = implode(' ', $vals);
						$this->add($mySqlCriterionField, "MATCH($mySqlField) AGAINST($val)", Criteria::CUSTOM);
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::NOT_IN:
					$vals = is_array($val) ? $val : explode(',', $val);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue))
							$vals[$valIndex] = '"' . $valValue . '"';
						else
							$vals[$valIndex] = $valValue;
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$val = '-' . implode(' -', $vals);
						$this->add($mySqlCriterionField, "MATCH($mySqlField) AGAINST($val)", Criteria::CUSTOM);
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::IN:
					$vals = is_array($val) ? $val : explode(',', $val);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue))
							$vals[$valIndex] = '"' . $valValue . '"';
						else
							$vals[$valIndex] = $valValue;
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, self::MAX_IN_VALUES);
						$val = '"' . implode('" "', $vals) . '"';
						$this->add($mySqlCriterionField, "MATCH($mySqlField) AGAINST($val)", Criteria::CUSTOM);
						$filter->unsetByName($field);
					}
					break;
				
				
				case baseObjectFilter::EQ:
					if(is_numeric($val) || strlen($val) > 1)
					{
						$val = '"' . $val . '"';
						$this->add($mySqlCriterionField, "MATCH($mySqlField) AGAINST($val)", Criteria::CUSTOM);
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::MULTI_LIKE_AND:
				case baseObjectFilter::MATCH_AND:
				case baseObjectFilter::LIKE:
					$vals = is_array($val) ? $val : explode(' ', $val);
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue))
							$vals[$valIndex] = '"' . $valValue . '"';
						else
							$vals[$valIndex] = $valValue;
					}
							
					if(count($vals))
					{
						$val = '+' . implode(' +', $vals);
						$this->add($mySqlCriterionField, "MATCH($mySqlField) AGAINST($val)", Criteria::CUSTOM);
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
	 * @todo change the advanced filter apply to attach crtieria or something
	 */
	protected function applyFilter(baseObjectFilter $filter)
	{
		$advancedSearch = $filter->getAdvancedSearch();
		if(is_object($advancedSearch))
		{
			KalturaLog::debug('Apply advanced filter [' . get_class($advancedSearch) . ']');
			if($advancedSearch instanceof AdvancedSearchFilterItem)
				$advancedSearch->apply($filter, $this, $this->matchClause, $this->whereClause);
		}
		else
		{
			KalturaLog::debug('No advanced filter found');
		}
		
		$this->applyFilterFields($filter);
		
		// attach all unhandled fields
		$filter->attachToFinalCriteria($this);
	}
}
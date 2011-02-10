<?php

abstract class MySqlCriteria extends KalturaCriteria
{
	/**
	 * Field keys to be removed from the criteria after all filters applied 
	 * @var array
	 */
	protected $keyToRemove = array();
		
	/**
	 * MySql where clauses
	 * @var array
	 */
	protected $whereClause = array();
	
	/**
	 * MySql textual match clauses
	 * @var array
	 */
	protected $matchClause = array();
	
	/**
	 * Counts how many criterions couldn't be handled
	 * @var int
	 */
	protected $criteriasLeft;

	/**
	 * @return criteriaFilter
	 */
	abstract protected function getDefaultCriteriaFilter();
	
	/**
	 * @return string
	 */
	abstract protected function getMySqlIndexName();
	
	/**
	 * @return array
	 */
	abstract public function getMySqlOrderFields();
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	abstract public function hasMySqlFieldName($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	abstract public function getMySqlFieldName($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	abstract public function getMySqlFieldType($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	abstract public function hasMatchableField($fieldName);
	
	/**
	 * @param string $index index name
	 * @param string $wheres
	 * @param string $orderBy
	 * @param string $limit
	 * @param int $maxMatches
	 * @param bool $setLimit
	 */
	abstract protected function executeMySql($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit);
	
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
		
		// go over all criterions and try to move them to the MySql
		foreach($this->getMap() as $field => $criterion)
		{
			if(!($criterion instanceof MySqlCriterion))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] is not MySql criteria");
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
			$matches = '(' . implode(') & (', $this->matchClause) . ')';
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
			$replace = $this->getMySqlOrderFields();
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
					KalturaLog::debug("Skip sort field[$orderField] from [$orderByColumn] limit won't be used in MySql query");
					$setLimit = false;
				}
			}
				
			if(count($orders))
				$orderBy = 'ORDER BY ' . implode(',', $orders);
		}
			
		$index = $this->getMySqlIndexName();
		$maxMatches = kMySqlSearchManager::MYSQL_MAX_RECORDS;
		$limit = $maxMatches;
		
		if($this->criteriasLeft)
			$setLimit = false;
			
		if($setLimit && $this->getLimit())
		{
			$maxMatches += $this->getOffset();
			$limit = $this->getLimit();
			if($this->getOffset())
				$limit = $this->getOffset() . ", $limit";
		}
		
		$this->executeMySql($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit);
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
			
			$fieldNamesArr = explode(baseObjectFilter::OR_SEPARATOR, $fieldName);
			if(count($fieldNamesArr) > 1)
			{
				$mySqlFieldNames = array();
				foreach($fieldNamesArr as $fieldName)
				{
					$mySqlField = $this->getMySqlFieldName($fieldName);
					$type = $this->getMySqlFieldType($mySqlField);
					$mySqlFieldNames[] = $mySqlField;
				}
				$mySqlField = '(' . implode(',', $mySqlFieldNames) . ')';
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
				$type = $this->getMySqlFieldType($mySqlField);
			}
			$valStr = print_r($val, true);
			
			KalturaLog::debug("Attach field[$fieldName] as MySql field[$mySqlField] of type [$type] and comparison[$operator] for value[$valStr]");
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
						$val = implode(' | ', $vals);
						$this->matchClause[] = "@$mySqlField $val";
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
						$val = '!' . implode(' & !', $vals);
						$this->matchClause[] = "@$mySqlField $val";
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
						$val = '(^' . implode('$ | ^', $vals) . '$)';
						$this->matchClause[] = "@$mySqlField $val";
						$filter->unsetByName($field);
					}
					break;
				
				
				case baseObjectFilter::EQ:
					if(is_numeric($val) || strlen($val) > 1)
					{
						$this->matchClause[] = "@$mySqlField ^$val$";
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
						$val = implode(' & ', $vals);
						$this->matchClause[] = "@$mySqlField $val";
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
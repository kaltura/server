<?php

class MySqlCriterion extends KalturaCriterion
{
	const MAX_IN_VALUES = 500;
	
	/**
	 * @var bool
	 */
	protected $hasOr = false;
	
	/**
	 * @var string Criteria class name
	 */
	protected $criteriaClass = false;
	
	public function __construct($criteriaClass, Criteria $criteria, $column, $value, $comparison = null)
	{
		$this->criteriaClass = $criteriaClass;
		
		parent::__construct($criteria, $column, $value, $comparison);
	}
	
	public function apply(array &$whereClause, array &$matchClause)
	{
		$field = $this->getTable() . '.' . $this->getColumn();
		
		if($this->hasOr)
		{
			KalturaLog::debug("Can't apply criterion [$field] has OR");
			return false;
		}
			
		KalturaLog::debug("Applies criterion [$field]");
	
		$clauses = $this->getClauses();
		if(count($clauses))
		{
			foreach($clauses as $clause)
			{
				if(!($clause instanceof MySqlCriterion))
				{
					KalturaLog::debug("Clause [" . $clause->getColumn() . "] is not Kaltura criteria");
					return false;
				}
					
				if(!$clause->apply($whereClause, $matchClause))
				{
					KalturaLog::debug("Failed to apply clause [" . $clause->getColumn() . "]");
					return false;
				}
			}
		}
		
		$comparison = $this->getComparison();
		if($comparison == Criteria::CUSTOM || $comparison == Criteria::CUSTOM_EQUAL || $comparison == Criteria::ISNOTNULL)
		{
			KalturaLog::debug("Skip criterion[$field] unhandled comparison [$comparison]");
			return false;
		}
	
		if(!$this->criteria->hasMySqlFieldName($field))
		{
			KalturaLog::debug("Skip criterion[$field] has no MySql field");
			return false;
		}
		
		$value = $this->getValue();
		
		$mySqlField	= $this->criteria->getMySqlFieldName($field);
		$type			= $this->criteria->getMySqlFieldType($mySqlField);
		
		if($field == entryPeer::ID)
		{
			if($comparison == Criteria::EQUAL)
				$comparison = Criteria::IN;
			if($comparison == Criteria::NOT_EQUAL)
				$comparison = Criteria::NOT_IN;
				
			if(!is_array($value))
				$value = explode(',', $value);
				
			$entryIds = array();
			foreach($value as $val)
				$entryIds[$val] = crc32($val);
				
			$value = $entryIds;
			$this->criteria->setEntryIds($comparison, $entryIds);
		}
		
		$valStr = print_r($value, true);
		KalturaLog::debug("Attach criterion[$field] as MySql field[$mySqlField] of type [$type] and comparison[$comparison] for value[$valStr]");
		
		if (is_string($value)) { // needed since otherwise the switch statement doesn't work as expected otherwise
			switch($value)
			{
				case Criteria::CURRENT_DATE:
					$d = getdate();
					$value = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
					break;
					
				case Criteria::CURRENT_TIME:
				case Criteria::CURRENT_TIMESTAMP:
					$value = time();
					break;
			}
		}
		
		if($type == 'string')
		{
			switch($comparison)
			{
				case Criteria::EQUAL:
					$matchClause[] = "@$mySqlField ^$value$";
					break;
					
				case Criteria::NOT_IN:
					$vals = is_array($value) ? $value : explode(',', $value);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = $valValue;
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, MySqlCriterion::MAX_IN_VALUES);
						$val = '!' . implode(' & !', $vals);
						$matchClause[] = "@$mySqlField $val";
					}
					break;
				
				case Criteria::IN:
					$vals = is_array($value) ? $value : explode(',', $value);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = $valValue;
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, MySqlCriterion::MAX_IN_VALUES);
						$val = '(^' . implode('$ | ^', $vals) . '$)';
						$matchClause[] = "@$mySqlField $val";
					}
					break;
					
				default:
					$matchClause[] = "@$mySqlField $value";
					break;
			}
			return true;
		}
		
		switch($comparison)
		{
			case Criteria::IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				sort($value); // importent, solves MySqlF IN bug
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)))
						unset($value[$valIndex]);
						
				$values = implode(',', $value);
				$whereClause[] = "$mySqlField in($values)";
				break;
				
			case Criteria::NOT_IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				foreach($value as $val)
					$whereClause[] = "$mySqlField != $val";
				break;
				
			case Criteria::ISNULL:
				$whereClause[] = "$mySqlField = 0";
				break;
				
			case Criteria::LESS_THAN:
			case Criteria::LESS_EQUAL:
				if($value > 0)
					$whereClause[] = "$mySqlField != 0";
				
			default:
				$whereClause[] = "$mySqlField $comparison $value";
				break;
		}
		return true;
	}


	/**
	 * Append an OR Criterion onto this Criterion's list.
	 * @return     Criterion
	 */
	public function addOr(Criterion $criterion)
	{
		$this->hasOr = true;
		return parent::addOr($criterion);
	}
}

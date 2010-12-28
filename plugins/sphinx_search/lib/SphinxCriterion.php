<?php

class SphinxCriterion extends KalturaCriterion
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
				if(!($clause instanceof SphinxCriterion))
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
	
		if(!$this->criteria->hasSphinxFieldName($field))
		{
			KalturaLog::debug("Skip criterion[$field] has no sphinx field");
			return false;
		}
		
		$value = $this->getValue();
		
		$sphinxField	= $this->criteria->getSphinxFieldName($field);
		$type			= $this->criteria->getSphinxFieldType($sphinxField);
		
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
		KalturaLog::debug("Attach criterion[$field] as sphinx field[$sphinxField] of type [$type] and comparison[$comparison] for value[$valStr]");
		
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
					$value = SphinxUtils::escapeString($value);
					$matchClause[] = "@$sphinxField ^$value$";
					break;
					
				case Criteria::NOT_IN:
					$vals = is_array($value) ? $value : explode(',', $value);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
						$val = '!' . implode(' & !', $vals);
						$matchClause[] = "@$sphinxField $val";
					}
					break;
				
				case Criteria::IN:
					$vals = is_array($value) ? $value : explode(',', $value);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue);
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
						$val = '(^' . implode('$ | ^', $vals) . '$)';
						$matchClause[] = "@$sphinxField $val";
					}
					break;
					
				default:
					$value = SphinxUtils::escapeString($value);
					$matchClause[] = "@$sphinxField $value";
					break;
			}
			return true;
		}
		
		switch($comparison)
		{
			case Criteria::IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				sort($value); // importent, solves sphinx IN bug
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)))
						unset($value[$valIndex]);
						
				$values = implode(',', $value);
				$whereClause[] = "$sphinxField in($values)";
				break;
				
			case Criteria::NOT_IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				foreach($value as $val)
					$whereClause[] = "$sphinxField != $val";
				break;
				
			case Criteria::ISNULL:
				$whereClause[] = "$sphinxField = 0";
				break;
				
			case Criteria::LESS_THAN:
			case Criteria::LESS_EQUAL:
				if($value > 0)
					$whereClause[] = "$sphinxField != 0";
				
			default:
				$whereClause[] = "$sphinxField $comparison $value";
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

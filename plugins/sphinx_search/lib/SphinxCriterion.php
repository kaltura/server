<?php

class SphinxCriterion extends KalturaCriterion
{
	const MAX_IN_VALUES = 150;
	
	/**
	 * @var bool
	 */
	protected $hasOr = false;
	
	/**
	 * 
	 * local whereClause
	 * @var array
	 */
	protected $whereClause = array();
	
	/**
	 * 
	 * local conditionClause
	 * @var array
	 */
	protected $conditionClause = array();
	
	public function apply(array &$whereClause, array &$matchClause, array &$conditionClause)
	{
		$field = $this->getTable() . '.' . $this->getColumn();
		
		if($this->hasOr)
		{
			KalturaLog::debug("criterion has OR");
			//return false;
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
				
				if(!$clause->apply($this->whereClause, $matchClause, $this->conditionClause))
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
		
		if($field == $this->criteria->getIdField())
		{
			if($comparison == Criteria::EQUAL)
				$comparison = Criteria::IN;
			if($comparison == Criteria::NOT_EQUAL)
				$comparison = Criteria::NOT_IN;
				
			if(!is_array($value))
				$value = explode(',', $value);
				
			$ids = array();
			foreach($value as $val)
				$ids[$val] = crc32($val);
				
			$value = $ids;
			$this->criteria->setIds($comparison, $ids);
		}
		
		$valStr = print_r($value, true);
		KalturaLog::debug("Attach criterion[$field] as sphinx field[$sphinxField] of type [$type] and comparison[$comparison] for value[$valStr]");
		
		if (is_null($value)){
			if ($comparison == Criteria::EQUAL)
				$comparison = Criteria::ISNULL;
			elseif ($comparison == Criteria::NOT_EQUAL)
				$comparison = Criteria::ISNOTNULL;
		}
		
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
		
		if($type == IIndexable::FIELD_TYPE_STRING)
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
						$val = $this->criteria->getPositiveMatch($sphinxField) .  ' !' . implode(' !', $vals);
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

				$value = array_slice($value, 0, SphinxCriterion::MAX_IN_VALUES);
				$values = implode(',', $value);
				$this->whereClause[] = "$sphinxField in($values)";
				break;
				
			case Criteria::NOT_IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				$value = array_slice($value, 0, SphinxCriterion::MAX_IN_VALUES);
				
				foreach($value as $val)
					$this->whereClause[] = "$sphinxField <> $val";
				break;
				
			case Criteria::ISNULL:
				$this->whereClause[] = "$sphinxField = 0";
				break;
				
			case Criteria::LESS_THAN:
			case Criteria::LESS_EQUAL:
				if($value > 0)
					$this->whereClause[] = "$sphinxField <> 0";
				// fallthrough
				
			default:
				$this->whereClause[] = "$sphinxField $comparison $value";
				break;
		}
		
		$this->whereClause = array_unique($this->whereClause);
		if(!count($this->whereClause))
			return true;
			
		if (!count($this->getConjunctions()) || in_array(Criterion::UND, $this->getConjunctions()))
		{
			$whereClause[] = implode(' AND ', $this->whereClause);
			foreach ($this->conditionClause as $eachConditionClause)
				$conditionClause[] = $eachConditionClause;
		}
		elseif (in_array(Criterion::ODER, $this->getConjunctions()))
		{
			$conditionClause[] = implode(' OR ', $this->whereClause);
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

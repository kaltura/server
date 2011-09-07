<?php

class SphinxCriterion extends KalturaCriterion
{
	const MAX_IN_VALUES = 150;
	
	protected function hasOr()
	{
		if (in_array(Criterion::ODER, $this->getConjunctions()))
		{
			return true;
		}

		foreach($this->getClauses() as $clause)
		{
			if (($clause instanceof SphinxCriterion) && $clause->hasOr())
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function apply(array &$whereClause, array &$matchClause, &$conditionClause, $depth = 0, $queryHasOr = true)
	{
		$field = $this->getTable() . '.' . $this->getColumn();
		
		if (!$depth && !$this->hasOr())
		{
			$queryHasOr = false;
		}
			
		KalturaLog::debug("Applies criterion [$field] queryHasOr=[$queryHasOr]");
	
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
		
		$thisClause = array();
		
		switch($comparison)
		{
			case Criteria::IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				sort($value); // importent, solves sphinx IN bug
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)) || (($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($valValue)))
						unset($value[$valIndex]);

				$value = array_slice($value, 0, SphinxCriterion::MAX_IN_VALUES);
				
				if (!count($value))
					break;
				
				if ($queryHasOr)
				{
					$inConditions = array();
					foreach($value as $val)
						 $inConditions[] = "$sphinxField = $val";
	
					$thisClause[] = implode(' OR ', $inConditions);
				}
				else 
				{
					$values = implode(',', $value);
					$thisClause[] = "$sphinxField in($values)";
				}
				break;
				
			case Criteria::NOT_IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				$value = array_slice($value, 0, SphinxCriterion::MAX_IN_VALUES);
				
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)) || (($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($valValue)))
						unset($value[$valIndex]);
				
				$notInConditions = array();
				foreach($value as $val)
					 $notInConditions[] = "$sphinxField <> $val";

				$thisClause[] = implode(' AND ', $notInConditions);
				break;
				
			case Criteria::ISNULL:
				$thisClause[] = "$sphinxField = 0";
				break;
				
			case Criteria::LESS_THAN:
			case Criteria::LESS_EQUAL:
				if($value > 0)
					$thisClause[] = "$sphinxField <> 0";
				// fallthrough
				
			default:
				if (!(($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($value)))
					$thisClause[] = "$sphinxField $comparison $value";
				break;
		}
		
		$thisClause = '(' . implode(' AND ', $thisClause) . ')';
		
		$clauses = $this->getClauses();
		$conjuctions = $this->getConjunctions();
		
		foreach($clauses as $index => $clause)
		{
			if(!($clause instanceof SphinxCriterion))
			{
				KalturaLog::debug("Clause [" . $clause->getColumn() . "] is not Kaltura criterion");
				return false;
			}
			
			$curConditionClause = '';
			if(!$clause->apply($whereClause, $matchClause, $curConditionClause, $depth + 1, $queryHasOr))
			{
				KalturaLog::debug("Failed to apply clause [" . $clause->getColumn() . "]");
				return false;
			}
			
			if (!$curConditionClause)
			{
				continue;
			}
			
			$thisClause .= $conjuctions[$index] .  $curConditionClause;
			if ($queryHasOr)
			{
				$thisClause = "($thisClause)";
			}
		}
		
		if ($queryHasOr)
		{
			$conditionClause = $thisClause;
		}
		else
		{
			$whereClause[] = $thisClause;
		}
		
		return true;
	}
}

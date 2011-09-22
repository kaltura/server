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
	
	protected function getStringMatchClause($sphinxField, $comparison, $value)
	{
		switch($comparison)
		{
			case Criteria::EQUAL:
				$value = SphinxUtils::escapeString($value);
				return "@$sphinxField ^$value$";
				
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
					return "@$sphinxField $val";
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
					return "@$sphinxField $val";
				}
				break;
				
			default:
				$value = SphinxUtils::escapeString($value);
				return "@$sphinxField $value";
		}
		
		return null;
	}
	
	protected function getNonStringClause($sphinxField, $type, $comparison, $value, $queryHasOr)
	{
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
				
				if (!count($value))
					break;
				
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
				if($type == IIndexable::FIELD_TYPE_DATETIME && $value > 0)
					$thisClause[] = "$sphinxField <> 0";
				// fallthrough
				
			default:
				if (!(($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($value)))
					$thisClause[] = "$sphinxField $comparison $value";
				break;
		}
		
		$thisClause = implode(' AND ', $thisClause);
		if ($queryHasOr && $thisClause)
		{
			$thisClause = "($thisClause)";
		}
		return $thisClause;
	}

	public function apply(array &$whereClause, array &$matchClause, &$conditionClause, $depth = 0, $queryHasOr = true)
	{
		if (!$depth && !$this->hasOr())
		{
			$queryHasOr = false;
		}
			
		$field = $this->getTable() . '.' . $this->getColumn();
		
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
			$curMatchClause = $this->getStringMatchClause($sphinxField, $comparison, $value);
			if ($curMatchClause)
				$matchClause[] = $curMatchClause;
			
			$thisClause = '';
		}
		else
		{
			$thisClause = $this->getNonStringClause($sphinxField, $type, $comparison, $value, $queryHasOr);
		}
		
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
			
			if ($thisClause)
			{
				$thisClause .= $conjuctions[$index];
			}
			$thisClause .= $curConditionClause;
			if ($queryHasOr)
			{
				$thisClause = "($thisClause)";
			}
		}
		
		if ($thisClause && !$queryHasOr)
		{
			$whereClause[] = $thisClause;
			$thisClause = null;
		}
			
		if ($thisClause && !$depth)
		{
			$expSimplifications = array(
				"(($sphinxField <> 0 AND $sphinxField <= $value) OR ($sphinxField = 0))"	=> "$sphinxField <= $value",
				"(($sphinxField <> 0 AND $sphinxField < $value) OR ($sphinxField = 0))"		=> "$sphinxField < $value",
			);

			if (array_key_exists($thisClause, $expSimplifications))
			{
				KalturaLog::debug("Simplifying expression [$thisClause] => [{$expSimplifications[$thisClause]}]");
				
				$whereClause[] = $expSimplifications[$thisClause];
				$thisClause = null;
			}
		}
		
		if ($thisClause)
		{
			$conditionClause = $thisClause;
		}
		
		return true;
	}
}

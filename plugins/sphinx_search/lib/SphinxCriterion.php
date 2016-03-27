<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCriterion extends KalturaCriterion implements IKalturaIndexQuery
{
	const MAX_IN_VALUES = 150;
	const MAX_UINT_VAL = 4294967296;
	const SPHINX_OR = '| ';
	const SPHINX_AND = '';
	
	protected static $NOT_NULL_FIELDS = array("created_at","updated_at");
	
	/**
	 * Sphinx textual match clauses
	 * @var array
	 */
	protected $matchClause = array();
	
	/**
	 * Sphinx condition clauses
	 * @var array
	 */
	protected $conditionClause = array();
	
	/**
	 * space or 
	 * @var string
	 */
	protected $selfMatchOperator = '';
	
	/**
	 * @var IKalturaIndexQuery
	 */
	protected $currentQuery;
	
	protected function needsBrackets()
	{
		$parentCriterion = $this->getParentCriterion();
		if(!$parentCriterion)
			return false;
			
		if($parentCriterion instanceof KalturaCriterion)
			return ($parentCriterion->getConjunction() == Criterion::ODER);
			
		return false;
	}
	
	protected function hasOr($checkParent = true)
	{
		$parentCriterion = $this->getParentCriterion();
		if($checkParent && $parentCriterion && $parentCriterion instanceof SphinxCriterion)
			return $parentCriterion->hasOr();
			
		if (in_array(Criterion::ODER, $this->getConjunctions()))
			return true;

		foreach($this->getClauses() as $clause)
		{
			if (($clause instanceof SphinxCriterion) && $clause->hasOr(false))
				return true;
		}
		
		return false;
	}
	
	protected function getStringMatchClause($sphinxField, $comparison, $value)
	{
		$obejctClass = $this->criteria->getIndexObjectName();
		$fieldsEscapeType = $obejctClass::getSearchFieldsEscapeType($sphinxField);
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$notEmpty = kSphinxSearchManager::HAS_VALUE . $partnerId;
		
		switch($comparison)
		{
			case Criteria::EQUAL:
				$value = SphinxUtils::escapeString($value, $fieldsEscapeType);
				if($obejctClass::isNullableField($sphinxField))
					return "@$sphinxField \\\"^$value $notEmpty$\\\"";
				else
					return "@$sphinxField \\\"^$value$\\\"";
				
			case Criteria::ISNULL:
				$isEmpty = kSphinxSearchManager::HAS_NO_VALUE . $partnerId;
				return "@$sphinxField $isEmpty";
				
			case Criteria::ISNOTNULL:
				return "@$sphinxField $notEmpty";
				
			case Criteria::NOT_IN:
				$vals = is_array($value) ? $value : explode(',', $value);
					
				foreach($vals as $valIndex => $valValue)
				{
					if(!is_numeric($valValue) && strlen($valValue) <= 1)
						unset($vals[$valIndex]);
					else
						$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
				}
				
				if(count($vals))
				{
					$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
					$val = $this->criteria->getFieldPrefix($sphinxField) .  ' !' . implode(' !', $vals);
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
						$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
				}
				
				if(count($vals))
				{
					$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
					$vals = array_filter($vals, 'trim');
					if($obejctClass::isNullableField($sphinxField))
						$val = "((\\\"^" . implode(" $notEmpty$\\\") | (\\\"^", $vals) . " $notEmpty$\\\"))";
					else
						$val = "((\\\"^" . implode("$\\\") | (\\\"^", $vals) . "$\\\"))";
					return "@$sphinxField $val";
				}
				break;
				
			case KalturaCriteria::IN_LIKE:
				$vals = is_array($value) ? $value : explode(',', $value);
					
				foreach($vals as $valIndex => $valValue)
				{
					if(!is_numeric($valValue) && strlen($valValue) <= 1)
						unset($vals[$valIndex]);
					else
						$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
				}
				
				if(count($vals))
				{
					$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
					$val = '(("' . implode('") | ("', $vals) . '"))';
					return "@$sphinxField $val";
				}
				break;
				
			case KalturaCriteria::IN_LIKE_ORDER:
				$vals = is_array($value) ? $value : explode(',', $value);
					
				foreach($vals as $valIndex => $valValue)
				{
					if(!is_numeric($valValue) && strlen($valValue) <= 1)
						unset($vals[$valIndex]);
					else
					{
						$valValue = explode(',', $valValue);
						$valValue = implode(' << ', $valValue);
						$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
					}
				}
				
				if(count($vals))
				{
					$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
					$val = '((' . implode(') | (', $vals) . '))';
					return "@$sphinxField $val";
				}
				break;

			case baseObjectFilter::MULTI_LIKE_AND:
			case baseObjectFilter::MATCH_AND:
					$vals = is_array($value) ? $value : explode(',', $value);
					foreach($vals as $valIndex => $valValue)
					{
						if(!strlen($valValue))
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue)) //if there are spaces or tabs - should add "<VALUE>"
							$vals[$valIndex] = '"' . SphinxUtils::escapeString($valValue, $fieldsEscapeType) . '"';
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
					}
							
					if(count($vals))
					{
						$val = implode(' ', $vals);
						return "(@$sphinxField $val)";
					}
					break;	
			case baseObjectFilter::MULTI_LIKE_OR:
			case baseObjectFilter::MATCH_OR:
					$vals = is_array($value) ? $value : explode(',', $value);
					foreach($vals as $valIndex => $valValue)
					{
						if(!strlen($valValue))
							unset($vals[$valIndex]);
						elseif(preg_match('/[\s\t]/', $valValue))
							$vals[$valIndex] = '"' . SphinxUtils::escapeString($valValue, $fieldsEscapeType) . '"';
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
					}
					
					if(count($vals))
					{
						$val = implode(' | ', $vals);
						return "(@$sphinxField $val)";
					}
					break;
				
			default:
				$value = SphinxUtils::escapeString($value, $fieldsEscapeType);
				return "@$sphinxField $value";
		}
		
		return null;
	}
	
	protected function generateAggregatedCondition($sphinxField, $value, $aggregatedStr, $singleStr)
	{
		$values = implode(',', $value);
		
		if ($this->hasOr())
		{
			return "$aggregatedStr ($sphinxField , $values)";
		}
		else
		{
			if(count($value) > 1)
			{
				return "$sphinxField $aggregatedStr($values)";
			}
			else
			{
				return "$sphinxField $singleStr $values";
			}
		}
	}
	
	protected function getNonStringClause($sphinxField, $type, $comparison, $value)
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
				
				$thisClause [] = $this->generateAggregatedCondition($sphinxField, $value, "in", "=");
				break;
				
			case Criteria::NOT_IN:
				$value = is_array($value) ? $value : explode(',', $value);
					
				$value = array_slice($value, 0, SphinxCriterion::MAX_IN_VALUES);
				
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)) || (($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($valValue)))
						unset($value[$valIndex]);
				
				if (!count($value))
					break;
				
				$thisClause [] = $this->generateAggregatedCondition($sphinxField, $value, "not in", "<>");
				
				break;
				
			case Criteria::ISNULL:
				$thisClause[] = "$sphinxField = 0";
				break;
				
			case Criteria::LESS_THAN:
			case Criteria::LESS_EQUAL:
				if($type == IIndexable::FIELD_TYPE_DATETIME && $value > 0) 
					if(!in_array($sphinxField, self::$NOT_NULL_FIELDS))
						$thisClause[] = "$sphinxField <> 0";
				// fallthrough
				
			default:
				if(is_bool($value))
					$value = intval($value);
					
				if (!(($type == IIndexable::FIELD_TYPE_INTEGER || $type == IIndexable::FIELD_TYPE_DATETIME) && !is_numeric($value)))
					$thisClause[] = "$sphinxField $comparison $value";
				break;
		}
		
		$thisClause = implode(' AND ', $thisClause);
		if ($this->hasOr() && $thisClause)
		{
			$thisClause = "($thisClause)";
		}
		return $thisClause;
	}

	/* (non-PHPdoc)
	 * @see KalturaCriterion::apply()
	 */
	public function apply(IKalturaIndexQuery $query)
	{
		$objectClass = $this->criteria->getIndexObjectName();
		$this->currentQuery = $query;
		
		list($field, $comparison, $value) = $this->criteria->translateSphinxCriterion($this);
		
		// Can apply criterion
		KalturaLog::info("Applies criterion [$field]");
	
		if(!$objectClass::hasIndexFieldName($field))
		{
			KalturaLog::debug("Skip criterion[$field] has no sphinx field");
			return false;
		}
				
		$sphinxField	= $objectClass::getIndexFieldName($field);
		$type			= $objectClass::getFieldType($sphinxField);
		
		if($comparison == Criteria::CUSTOM || $comparison == Criteria::CUSTOM_EQUAL || 
				($comparison == Criteria::ISNOTNULL && $type != IIndexable::FIELD_TYPE_STRING))
		{
			KalturaLog::debug("Skip criterion[$field] unhandled comparison [$comparison]");
			return false;
		}
		
		// Update value & comparison in case of id field
		if($field == $objectClass::getIdField())
		{
			if($comparison == Criteria::EQUAL)
				$comparison = Criteria::IN;
			if($comparison == Criteria::NOT_EQUAL)
				$comparison = Criteria::NOT_IN;
				
			if(!is_array($value))
				$value = explode(',', $value);
				
			$ids = array();
			
			foreach($value as $val)
				$ids[$val] = $this->criteria->getTranslateIndexId($val);

			$value = $ids;
			$this->criteria->setIds($comparison, $ids);
		}
		
		if($objectClass::getFieldType($sphinxField, true) == IIndexable::FIELD_TYPE_UINT)
		{
			switch (gettype($value))
			{
				case 'int':
					if($value < 0)
					$value = self::MAX_UINT_VAL + $value;
					break;
				case 'string':
					$value = explode(",", $value);
				case 'array':
					if(count($value) == 1 && isset($value[0]))
					{
						$value = (int)$value[0];
						if($value < 0)
							$value = self::MAX_UINT_VAL + $value;
						$value = (string)$value;
					}
					else
					{
						foreach($value as $index => $arrVal)
						{
							if($arrVal < 0)
								$value[$index] = self::MAX_UINT_VAL + $arrVal;
						}
					}
					break;
			}
		}
	
		$valStr = print_r($value, true);
		KalturaLog::debug("Attach criterion[$field] as sphinx field[$sphinxField] of type [$type] and comparison[$comparison] for value[$valStr]");
		
		// update comparison in case of null
		if (is_null($value)){
			if ($comparison == Criteria::EQUAL)
				$comparison = Criteria::ISNULL;
			elseif ($comparison == Criteria::NOT_EQUAL)
				$comparison = Criteria::ISNOTNULL;
		}
		
		// update value in case of Date
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
			$match = $this->getStringMatchClause($sphinxField, $comparison, $value);
			KalturaLog::debug("Add match criterion[$field] as sphinx field[$sphinxField] of type [$type] match [$match] line [" . __LINE__ . "]");
			$this->addMatch($match);
		}
		else
		{
			$condition = $this->getNonStringClause($sphinxField, $type, $comparison, $value);
			KalturaLog::debug("Add condition criterion[$field] as sphinx field[$sphinxField] of type [$type] condition [$condition] line [" . __LINE__ . "]");
			$this->addCondition($condition);
		}
		
		$clauses = $this->getClauses();
		foreach($clauses as $index => $clause)
		{
			if(!($clause instanceof SphinxCriterion))
			{
				KalturaLog::debug("Clause [" . $clause->getColumn() . "] is not Sphinx criterion");
				return false;
			}
			
			if(!$clause->apply($this))
			{
				KalturaLog::debug("Failed to apply clause [" . $clause->getColumn() . "]");
				return false;
			}
		}
	
		if (count($this->matchClause))
		{
			$matchesClause = implode(' ', $this->matchClause);
			if ($this->hasOr() && count($this->matchClause) > 1)
				$matchesClause = "($matchesClause)";
			
			$match = $this->getSelfMatchOperator() . $matchesClause;
			KalturaLog::debug("Add match criterion[$field] as sphinx field[$sphinxField] of type [$type] match [$match] line [" . __LINE__ . "]");
			$query->addMatch($match);
		}
		
		if (count($this->conditionClause))
		{
			$attributesClause = implode($this->getConjunction(), array_unique($this->conditionClause));
			if(!strlen(trim($attributesClause)))
				return true;
				
			if (!$this->hasOr())
			{
				$where = $attributesClause;
				KalturaLog::debug("Add where criterion[$field] as sphinx field[$sphinxField] of type [$type] where [$where] line [" . __LINE__ . "]");
				$query->addWhere($where);
				return true;
			}
			
			// Reduce null
			$expSimplifications = array(
				"($sphinxField <> 0 AND $sphinxField <= $value) OR ($sphinxField = 0)"	=> "$sphinxField <= $value",
				"($sphinxField <> 0 AND $sphinxField < $value) OR ($sphinxField = 0)"		=> "$sphinxField < $value",
			);

			if (isset($expSimplifications[$attributesClause]))
			{
				KalturaLog::debug("Simplifying expression [$attributesClause] => [{$expSimplifications[$attributesClause]}]");
				
				// We move it to the 'where' since where is allegedly faster than in the condition.
				$where = $this->getConjunction() . $expSimplifications[$attributesClause];
				KalturaLog::debug("Add where criterion[$field] as sphinx field[$sphinxField] of type [$type] where [$where] line [" . __LINE__ . "]");
				$query->addWhere($where);
			}
			else
			{
				if($this->needsBrackets())
					$attributesClause = "($attributesClause)";
					
				$condition = $this->getConjunction() . $attributesClause;
				KalturaLog::debug("Add condition criterion[$field] as sphinx field[$sphinxField] of type [$type] condition [$condition] line [" . __LINE__ . "]");
				$query->addCondition($condition);
			}
		}
		
		return true;
	}

	/**
	 * @return string $selfMatchOperator
	 */
	protected function getSelfMatchOperator()
	{
		return $this->selfMatchOperator;
	}

	/**
	 * @param string $selfMatchOperator
	 */
	protected function setSelfMatchOperator($selfMatchOperator)
	{
		$this->selfMatchOperator = $selfMatchOperator;
	}
	
	/* (non-PHPdoc)
	 * @see Criterion::addAnd()
	 */
	public function addAnd(Criterion $criterion)
	{
		if($criterion instanceof SphinxCriterion)
			$criterion->setSelfMatchOperator(self::SPHINX_AND);
			
		return parent::addAnd($criterion);
	}

	/* (non-PHPdoc)
	 * @see Criterion::addOr()
	 */
	public function addOr(Criterion $criterion)
	{
		$objectClass = $this->criteria->getIndexObjectName();
		$currentField = $this->getTable() . '.' . $this->getColumn();
		$addedField = $this->getTable() . '.' . $this->getColumn();
		KalturaLog::debug("Add OR criterion field [$addedField] to current field [$currentField]");
	
		// Validate that the added criterion and the current criterios are both attributes or both matches
		if($objectClass::hasIndexFieldName($addedField))
		{		
			$currentSphinxField	= $objectClass::getIndexFieldName($currentField);
			$addedSphinxField	= $objectClass::getIndexFieldName($addedField);
			
			if($currentSphinxField != $addedSphinxField)
			{
				KalturaLog::debug("Current sphinx field [$currentSphinxField] and added sphinx field [$addedField]");
				
				$currentType	= $objectClass::getFieldType($currentSphinxField);
				$addedType		= $objectClass::getFieldType($addedField);
				
				if($currentType != $addedType)
				{
					KalturaLog::debug("Current type [$currentType] and added type [$addedType]");
					if($currentType == IIndexable::FIELD_TYPE_STRING || $addedType == IIndexable::FIELD_TYPE_STRING)
						throw new kCoreException("Cannot mix OR operator on attributes and matches", kCoreException::INVALID_QUERY);
				}
			}
		}
		
		if($criterion instanceof SphinxCriterion)
			$criterion->setSelfMatchOperator(self::SPHINX_OR);
			
		return parent::addOr($criterion);
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addWhere()
	 */
	public function addWhere($statement)
	{
		if($this->currentQuery)
			$this->currentQuery->addWhere($statement);
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addMatch()
	 */
	public function addMatch($match)
	{
		if(strlen(trim($match)))
			$this->matchClause[] = $match;
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addCondition()
	 */
	public function addCondition($condition)
	{
		if(strlen(trim($condition)))
			$this->conditionClause[] = $condition;
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addOrderBy()
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC)
	{
		if($this->currentQuery)
			$this->currentQuery->addOrderBy($column, $orderByType);
	}
	
	public function getPossibleValues($recursive = true) {
		
		// In case we have an or inside the criterion - we can't handle it.
		if (in_array ( Criterion::ODER, $this->getConjunctions () ))
			return null;
		
		if ($this->getComparison () == Criteria::EQUAL) {
			$res = array ();
			$res [] = $this->getValue ();
			return $res;
		}
		
		if ($this->getComparison () == Criteria::IN) {
			$value = $this->getValue ();
			if (is_string ( $value ))
				return explode ( ",", $value );
			return $value;
		}
		
		if ($recursive) {
			foreach ( $this->getClauses () as $criterions ) {
				$values = $criterions->getPossibleValues ( false );
				if (! is_null ( $values )) {
					return $values;	
				}
			}
		}
		return null;
	}
	
}

<?php

class SolrEntryCriterion extends Criterion
{
	/**
	 * @var bool
	 */
	private $hasOr = false;
	
	/**
	 * @var SolrEntryCriteria
	 */
	private $criteria = false;
	
	public function __construct(Criteria $criteria, $column, $value, $comparison = null)
	{
		$this->criteria = $criteria;
		
		parent::__construct($criteria, $column, $value, $comparison);
	}
	
	public function apply(array &$whereClause)
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
				if(!($clause instanceof SolrEntryCriterion))
				{
					KalturaLog::debug("Clause [" . $clause->getColumn() . "] is not solr criteria");
					return false;
				}
					
				if(!$clause->apply($whereClause))
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
	
		if(!isset(SolrEntryCriteria::$solrFields[$field]))
		{
			KalturaLog::debug("Skip criterion[$field] has no solr field");
			return false;
		}
		
		$value = $this->getValue();
		
		$solrField = SolrEntryCriteria::getSolrFieldName($field);
		$type = SolrEntryCriteria::getSolrFieldType($solrField);
		
		$valStr = print_r($value, true);
		KalturaLog::debug("Attach criterion[$field] as solr field[$solrField] of type [$type] and comparison[$comparison] for value[$valStr]");
		
		if (is_string($value)) { // needed since otherwise the switch statement doesn't work as expected
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
					$whereClause[] = "$solrField:$value";
					break;
					
				case Criteria::NOT_IN:
					$vals = array();
					if(is_string($value))
						$vals = explode(',', $val);
					elseif(is_array($value))
						$vals = $value;
						
					foreach($vals as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, SolrEntryCriteria::MAX_IN_VALUES);
						$whereClause[] = "-$solrField:(" . implode(" ", $vals) . ")";
					}
					break;
				
				case Criteria::IN:
					$vals = array();
					if(is_string($value))
						$vals = explode(',', $value);
					elseif(is_array($value))
						$vals = $value;
						
					foreach($vals as $valIndex => $valValue)
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($vals[$valIndex]);
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, SolrEntryCriteria::MAX_IN_VALUES);
						$whereClause[] = "$solrField:(" . implode(" ", $vals) . ")";
					}
					break;
					
				default:
					$whereClause[] = "$solrField:$value";
					break;
			}
			return true;
		}
		
		if ($type == 'date')
		{
			$value = strftime("%Y-%m-%dT%H:%M:%SZ", $value);
		}
		
		switch($comparison)
		{
			case Criteria::IN:
				if(!is_array($value))
					$value = explode(',', $value);
					
				sort($value); // importent, solves solr IN bug
				foreach($value as $valIndex => $valValue)
					if(is_null($valValue) || !strlen(trim($valValue)))
						unset($value[$valIndex]);
						
				$whereClause[] = "$solrField:(" . implode(" ", $value) . ")";
				break;
				
			case Criteria::NOT_IN:
				if(!is_array($value))
					$value = explode(',', $value);
					
				$whereClause[] = "-$solrField:(" . implode(" ", $value) . ")";
				break;
				
			case Criteria::ISNULL:
				$whereClause[] = "$solrField:0";
				break;
				
			case Criteria::LESS_THAN:
				$whereClause[] = $solrField.':{* TO'.$value.'}';
				break;
				
			case Criteria::LESS_EQUAL:
				$whereClause[] = "$solrField:[* TO $value]";
				break;

			case Criteria::GREATER_THAN:
				$whereClause[] = $solrField.':{'.$value.' TO *}';
				break;
				
			case Criteria::GREATER_EQUAL:
				$whereClause[] = "$solrField:[$value TO *]";
				break;

			case Criteria::EQUAL:
				$whereClause[] = "$solrField:$value";
				break;

			case Criteria::NOT_EQUAL:
				$whereClause[] = "-$solrField:$value";
				break;
				
			default:
				KalturaLog::debug("Attach criterion[$field] as solr field[$solrField] of type [$type] and comparison[$comparison] for value[$value] failed");
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
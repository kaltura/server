<?php

abstract class KalturaCriterion extends Criterion
{
	/**
	 * @var KalturaCriteria
	 */
	protected $criteria = false;
	
	public function __construct(Criteria $criteria, $column, $value, $comparison = null)
	{
		$this->criteria = $criteria;
		
		parent::__construct($criteria, $column, $value, $comparison);
	}
	
	abstract public function apply(array &$whereClause, array &$matchClause);
}
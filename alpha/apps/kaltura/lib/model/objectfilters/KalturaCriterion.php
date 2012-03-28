<?php
/**
 * @package Core
 * @subpackage model.filters
 */
abstract class KalturaCriterion extends Criterion
{
	/**
	 * @var KalturaCriterion
	 */
	protected $parentCriterion = null;
	
	/**
	 * @var KalturaCriteria
	 */
	protected $criteria = false;
	
	/**
	 * AND or OR
	 * @var string
	 */
	protected $selfConjunction = '';
	
	/**
	 * @param Criteria $criteria
	 * @param string $column
	 * @param string $value
	 * @param string $comparison
	 */
	public function __construct(Criteria $criteria, $column, $value, $comparison = null)
	{
		$this->criteria = $criteria;
		
		parent::__construct($criteria, $column, $value, $comparison);
	}

	/**
	 * @param IKalturaIndexQuery $query
	 * @param int $depth
	 * @param bool $queryHasOr
	 */
	abstract public function apply(IKalturaIndexQuery $query);
	
	/* (non-PHPdoc)
	 * @see Criterion::addAnd()
	 */
	public function addAnd(Criterion $criterion)
	{
		if($criterion instanceof KalturaCriterion)
		{
			$criterion->setParentCriterion($this);
			$criterion->setSelfConjunction(self::UND);
		}
			
		return parent::addAnd($criterion);
	}

	/* (non-PHPdoc)
	 * @see Criterion::addOr()
	 */
	public function addOr(Criterion $criterion)
	{
		if($criterion instanceof KalturaCriterion)
		{
			$criterion->setSelfConjunction(self::ODER);
			$criterion->setParentCriterion($this);
		}
			
		return parent::addOr($criterion);
	}
	
	/**
	 * @return KalturaCriterion $parentCriterion
	 */
	protected function getParentCriterion()
	{
		return $this->parentCriterion;
	}

	/**
	 * @param KalturaCriterion $parentCriterion
	 */
	protected function setParentCriterion(KalturaCriterion $parentCriterion)
	{
		$this->parentCriterion = $parentCriterion;
	}
	
	/**
	 * @return string $selfConjunction
	 */
	protected function getSelfConjunction()
	{
		return $this->selfConjunction;
	}

	/**
	 * @param string $selfConjunction
	 */
	protected function setSelfConjunction($selfConjunction)
	{
		$this->selfConjunction = $selfConjunction;
	}
}
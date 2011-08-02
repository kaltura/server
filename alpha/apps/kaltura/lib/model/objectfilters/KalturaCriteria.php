<?php

class KalturaCriteria extends Criteria
{
	/**
	 * The count of total returned items
	 * @var int
	 */
	protected $recordsCount = 0;
	
	/**
	 * Array of ids that were retured from the execution 
	 * @var array
	 */
	protected $fetchedIds = array();
	
	/**
	 * @var array<baseObjectFilter>
	 */
	protected $filters = array();
		
	/**
	 * Execute count query after apply filters 
	 * @var bool
	 */
	protected $doCount = true;
	
	/**
	 * @return int $recordsCount
	 */
	public function getRecordsCount() 
	{
		return $this->recordsCount;
	}
	
	/**
	 * 
	 * return fetchedIds
	 */
	public function getFetchedIds()
	{
		return $this->fetchedIds;
	}
	
	/**
	 * 
	 * set fetchedIds
	 * @param array $fetchedIds
	 */
	public function setFetchedIds($fetchedIds)
	{
		$this->fetchedIds = $fetchedIds;
	}
	
	public function getIdField()
	{
		return null;
	}
	
	/**
	 * @param int $recordsCount
	 */
	public function setRecordsCount($recordsCount) 
	{
		$this->recordsCount = $recordsCount;
	}
	
	public function dontCount() 
	{
		$this->doCount = false;
	}
	
	/**
	 * Store the filter as is
	 * Later the filter fields and the advanced search will be used to attach additional criterions
	 * 
	 * @param baseObjectFilter $filter
	 */
	public function attachFilter(baseObjectFilter $filter)
	{
		foreach($this->filters as $existsFilter)
			if($existsFilter === $filter)
				return;
				
		$this->filters[] = $filter;
	}
	
	/**
	 * Applies all filter on this criteria
	 */
	public function applyFilters()
	{
		foreach($this->filters as $filter)
			$filter->attachToFinalCriteria($this);
	}
	
	/**
	 * Creates a new KalturaCriteria for the given object name
	 * 
	 * @param string $objectType object type to create Criteria for.
	 * @return KalturaCriteria derived object
	 */
	public static function create($objectType)
	{
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCriteriaFactory');
		foreach($pluginInstances as $pluginInstance)
		{
			$criteria = $pluginInstance->getKalturaCriteria($objectType);
			if($criteria)
				return $criteria;
		}
			
		return new KalturaCriteria();
	}
	
	/**
	 * 
	 * sort array orderby from this criteria
	 * @param array $queryResult
	 */
	public function applyResultsSort(array &$queryResult){
		return;
	}

	/* (non-PHPdoc)
	 * @see Criteria::getNewCriterion()
	 */
	public function getNewCriterion($column, $value, $comparison = null)
	{
		return new Criterion($this, $column, $value, $comparison);
	}

	/* (non-PHPdoc)
	 * @see Criteria::add()
	 */
	public function add($p1, $value = null, $comparison = null)
	{
		if ($p1 instanceof Criterion) {
			$this->map[$p1->getTable() . '.' . $p1->getColumn()] = $p1;
		} else {
			$this->map[$p1] = $this->getNewCriterion($p1, $value, $comparison);
		}
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see Criteria::addAnd()
	 */
	public function addAnd($p1, $p2 = null, $p3 = null)
	{
		if ($p3 !== null) {
			// addAnd(column, value, comparison)
			$oc = $this->getCriterion($p1);
			$nc = $this->getNewCriterion($p1, $p2, $p3);
			if ( $oc === null) {
				$this->map[$p1] = $nc;
			} else {
				$oc->addAnd($nc);
			}
		} elseif ($p2 !== null) {
			// addAnd(column, value)
			$this->addAnd($p1, $p2, self::EQUAL);
		} elseif ($p1 instanceof Criterion) {
			// addAnd(Criterion)
			$oc = $this->getCriterion($p1->getTable() . '.' . $p1->getColumn());
			if ($oc === null) {
				$this->add($p1);
			} else {
				$oc->addAnd($p1);
			}
		} elseif ($p2 === null && $p3 === null) {
			// client has not specified $p3 (comparison)
			// which means Criteria::EQUAL but has also specified $p2 == null
			// which is a valid combination we should handle by creating "IS NULL"
			$this->addAnd($p1, $p2, self::EQUAL);
		}
		return $this;
	}

	/* (non-PHPdoc)
	 * @see Criteria::addOr()
	 */
	public function addOr($p1, $p2 = null, $p3 = null)
	{
		if ($p3 !== null) {
			// addOr(column, value, comparison)
			$nc = $this->getNewCriterion($p1, $p2, $p3);
			$oc = $this->getCriterion($p1);
			if ($oc === null) {
				$this->map[$p1] = $nc;
			} else {
				$oc->addOr($nc);
			}
		} elseif ($p2 !== null) {
			// addOr(column, value)
			$this->addOr($p1, $p2, self::EQUAL);
		} elseif ($p1 instanceof Criterion) {
			// addOr(Criterion)
			$oc = $this->getCriterion($p1->getTable() . '.' . $p1->getColumn());
			if ($oc === null) {
				$this->add($p1);
			} else {
				$oc->addOr($p1);
			}
		} elseif ($p2 === null && $p3 === null) {
			// client has not specified $p3 (comparison)
			// which means Criteria::EQUAL but has also specified $p2 == null
			// which is a valid combination we should handle by creating "IS NULL"
			$this->addOr($p1, $p2, self::EQUAL);
		}

		return $this;
	}
}
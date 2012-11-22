<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class KalturaCriteria extends Criteria implements IKalturaDbQuery
{
	const IN_LIKE = 'IN_LIKE';
	const IN_LIKE_ORDER ='IN_LIKE_ORDER';
	
	protected static $maxRecords = 1000;
	
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
	 * @return int
	 */
	public static function getMaxRecords() 
	{
		return self::$maxRecords;
	}

	/**
	 * @param int $maxRecords
	 */
	public static function setMaxRecords($maxRecords) 
	{
		self::$maxRecords = $maxRecords;
	}
	
	/**
	 * @return int $recordsCount
	 */
	public function getRecordsCount() 
	{
		return $this->recordsCount;
	}
	
	/**
	 * return array fetchedIds
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
	public function setFetchedIds(array $fetchedIds)
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
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilter(baseObjectFilter $filter)
	{
		$advancedSearch = $filter->getAdvancedSearch();
		if(is_object($advancedSearch))
		{
			KalturaLog::debug('Apply advanced filter [' . get_class($advancedSearch) . ']');
			if($advancedSearch instanceof AdvancedSearchFilterItem)
				$advancedSearch->apply($filter, $this);
				
			$this->hasAdvancedSearchFilter = true;
		}
		else
		{
			KalturaLog::debug('No advanced filter found');
		}
		
		// attach all unhandled fields
		$filter->attachToFinalCriteria($this);
	}
	
	/**
	 * Applies all filter on this criteria
	 */
	public function applyFilters()
	{
		foreach($this->filters as $filter)
			$this->applyFilter($filter);
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
	 * @return array
	 */
	public function applyResultsSort(array $queryResult){
		return $queryResult;
	}

	/* (non-PHPdoc)
	 * @see Criteria::add()
	 */
	public function add($p1, $value = null, $comparison = null)
	{
		if ($p1 instanceof Criterion)
		{
			$oc = $this->getCriterion($p1->getColumn());
			if(!is_null($oc) && $oc->getValue() == $p1->getValue() && $oc->getComparison() != $p1->getComparison())
				return $this;
				
			return parent::add($p1);
		}
		
		$nc = $this->getNewCriterion($p1, $value, $comparison);
		return parent::add($nc);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#addAnd()
	 */
	public function addAnd($p1, $p2 = null, $p3 = null)
	{
		if (is_null($p3)) 
			return parent::addAnd($p1, $p2, $p3);
			
		// addAnd(column, value, comparison)
		$nc = $this->getNewCriterion($p1, $p2, $p3);
		$oc = $this->getCriterion($p1);
		
		if ( !is_null($oc) )
		{
			// no need to add again
			if($oc->getValue() != $p2 || $oc->getComparison() != $p3)
				$oc->addAnd($nc);
				
			return $this;
		}
			
		return $this->add($nc);
	}
	
	/* (non-PHPdoc)
	 * @see Criteria::addOr()
	 */
	public function addOr($p1, $p2 = null, $p3 = null)
	{
		if(is_null($p3))
			 return parent::addOr($p1, $p2, $p3);
			 
		// addOr(column, value, comparison)
		$nc = $this->getNewCriterion($p1, $p2, $p3);
		$oc = $this->getCriterion($p1);
		
		if ( !is_null($oc) )
		{
			// no need to add again
			if($oc->getValue() != $p2 || $oc->getComparison() != $p3)
				$oc->addOr($nc);
				
			return $this;
		}
		
		return parent::add($nc);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaDbQuery::addColumnWhere()
	 */
	public function addColumnWhere($column, $value, $comparison)
	{
		$this->add($column, $value, $comparison);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaDbQuery::addOrderBy()
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC)
	{
		if($orderByType == Criteria::ASC)
		{
			$this->addAscendingOrderByColumn($column);
		}
		else
		{
			$this->addDescendingOrderByColumn($column);
		}
	}
}
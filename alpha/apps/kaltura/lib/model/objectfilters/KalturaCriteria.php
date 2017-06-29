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

	protected $selectColumn = null;
	
	protected $groupByColumn = null;
	
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
		$pluginClassName = null;
		$cacheKey = null;
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_APC_LCAL);
		if($cache)
		{
			$cacheKey = "criteriaClass-$objectType";
			$pluginClassName = $cache->get($cacheKey);
		}
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCriteriaFactory', $pluginClassName);
		foreach($pluginInstances as $pluginInstance)
		{
			$criteria = $pluginInstance->getKalturaCriteria($objectType);
			if($criteria)
			{
				if (!$pluginClassName && $cacheKey)
				{
					if($cache)
					{
						$cache->set($cacheKey, get_class($pluginInstance));
					}
				}
				return $criteria;
			}
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
	
	public function setGroupByColumn($name)
	{
		$this->groupByColumn = $name;
	}
	
	public function setSelectColumn($name)
	{
		$this->selectColumn = $name;
	}

	public static function escapeString($str, $escapeType = SearchIndexFieldEscapeType::DEFAULT_ESCAPE, $iterations = 2)
	{
		if($escapeType == SearchIndexFieldEscapeType::DEFAULT_ESCAPE)
		{
			// NOTE: it appears that sphinx performs double decoding on SELECT values, so we encode twice.
			//		" and ! are escaped once to enable clients to use them, " = exact match, ! = AND NOT
			//	This code could have been implemented more elegantly using array_map, but this implementation is the fastest
			$from 		= array ('\\', 		'"', 		'!',		'(',		')',		'|',		'-',		'@',		'~',		'&',		'/',		'^',		'$',		'=',		'_',		'%', 		'\'',		'<',		'>',		);

			if ($iterations == 2)
			{
				$toDouble   = array ('\\\\', 	'\\"', 		'\\!',		'\\\\\\(',	'\\\\\\)',	'\\\\\\|',	'\\\\\\-',	'\\\\\\@',	'\\\\\\~',	'\\\\\\&',	'\\\\\\/',	'\\\\\\^',	'\\\\\\$',	'\\\\\\=',	'\\\\\\_',	'\\\\\\%',	'\\\\\\\'',	'\\\\\\<',	'\\\\\\>',	);
				return str_replace($from, $toDouble, $str);
			}
			else
			{
				$toSingle   = array ('\\\\', 	'\\"', 		'\\!',		'\\(',		'\\)',		'\\|',		'\\-',		'\\@',		'\\~',		'\\&',		'\\/',		'\\^',		'\\$',		'\\=',		'\\_',		'\\%',		'\\\'',		'\\<',		'\\>',		);
				return str_replace($from, $toSingle, $str);
			}
		}
		elseif($escapeType == SearchIndexFieldEscapeType::MD5_LOWER_CASE)
		{
			$str = strtolower($str);

			if(substr($str, -2) == '\*')
				return md5(substr($str, 0, strlen($str) - 2)) . '\\\*';

			$md5Str = md5($str);
			KalturaLog::debug('md5(' . $str . ')' . ' = ' . $md5Str );

			return $md5Str;
		}
		elseif($escapeType == SearchIndexFieldEscapeType::NO_ESCAPE)
		{
			return $str;
		}
	}
}
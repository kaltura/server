<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
abstract class SphinxCriteria extends KalturaCriteria implements IKalturaIndexQuery
{
	const RANKER_NONE = 'none';
	const RANKER_BM25 = 'BM25';
	const WEIGHT = '@weight';
	const MAX_MATCHES = 10000;
	
	/**
	 * @var string none or sph04
	 */
	protected $ranker;
	
	/**
	 * Field keys to be removed from the criteria after all filters applied 
	 * @var array
	 */
	protected $keyToRemove = array();
		
	/**
	 * Sphinx where clauses
	 * @var array
	 */
	protected $whereClause = array();
	
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
	 * Sphinx orderby clauses
	 * @var array
	 */
	protected $orderByClause = array();
	
	/**
	 * Indicates that order by clauses added and the results should be sorted
	 * @var bool
	 */
	protected $applySortRequired;
	
	/**
	 * Counts how many criterions couldn't be handled
	 * @var int
	 */
	protected $criteriasLeft;
	
	/**
	 * Array of specific ids that could be returned
	 * Used for _in_id and _eq_id filter fields 
	 * The form is array[$operator] = array($entryId1 => $entryCrc1, $entryId2 => $entryCrc2)
	 * @var array
	 */
	protected $ids = array();
	
	protected $hasAdvancedSearchFilter = false;
	
	protected $sphinxSkipped = false;
	
	protected function applyIds(array $ids)
	{
		if(!count($this->ids))
			return $ids;
			
		foreach($this->ids as $comparison => $theIds)
		{
			// keeps only ids that appears in both arrays
			if($comparison == Criteria::IN)
			{
				$ids = array_intersect($ids, array_keys($theIds));
			}
			
			// removes ids that appears in the comparison array
			if($comparison == Criteria::NOT_IN)
			{
				$ids = array_diff($ids, array_keys($theIds));
			}
		}
		return $ids;
	}
	
	public function setIds($comparison, $ids)
	{
		$this->ids[$comparison] = $ids;
	}
	
	public function getPositiveMatch($field)
	{
		return '';
	}
	
    protected function getEnableStar ()
	{
	    return false;
	}
	
	/**
	 * @return criteriaFilter
	 */
	abstract protected function getDefaultCriteriaFilter();
	
	/**
	 * @return string
	 */
	abstract protected function getSphinxIndexName();
	
	/**
	 * @return array
	 */
	abstract public function getSphinxOrderFields();
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	abstract public function hasSphinxFieldName($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	abstract public function getSphinxFieldName($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	abstract public function getSphinxFieldType($fieldName);
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	abstract public function hasMatchableField($fieldName);
	
	/**
	 * @return string
	 */
	abstract protected function getSphinxIdField();
	
	/**
	 * @return string
	 */
	abstract protected function getPropelIdField();
	
	/**
	 * @param Criteria $c
	 * @return int
	 */
	abstract protected function doCountOnPeer(Criteria $c);
	
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}
	
	public function isNullableField($fieldName)
	{
		return false;
	}
	
	/**
	 * @param string $index index name
	 * @param string $wheres
	 * @param string $orderBy
	 * @param string $limit
	 * @param int $maxMatches
	 * @param bool $setLimit
	 * @param string $conditions
	 */
	protected function executeSphinx($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit, $conditions = '')
	{
		$pdo = DbManager::getSphinxConnection();
		
		if (!$this->selectColumn)
			$this->selectColumn = $this->getSphinxIdField();
		
		$sql = "SELECT {$this->selectColumn} $conditions FROM $index $wheres $orderBy " . ($this->groupByColumn ? "GROUP BY {$this->groupByColumn} " : "" ) . "LIMIT $limit OPTION ranker={$this->ranker}, max_matches=$maxMatches, comment='".kApiCache::KALTURA_COMMENT_MARKER."'";
		
		if (kConf::hasParam('sphinx_extra_options'))
			$sql .= ', ' . kConf::get('sphinx_extra_options');

		$badSphinxQueries = kConf::hasParam("sphinx_bad_queries") ? kConf::get("sphinx_bad_queries") : array();

		foreach($badSphinxQueries as $badQuery)
		{
			if (preg_match($badQuery, $sql))
			{
				KalturaLog::log("bad sphinx query: [$badQuery] $sql");
				throw new kCoreException("Invalid sphinx query [$sql]\nMatched regular expression [$badQuery]", APIErrors::SEARCH_ENGINE_QUERY_FAILED);
			}
		}

		//debug query
		$ids = $pdo->queryAndFetchAll($sql, PDO::FETCH_COLUMN, 0);
		if($ids === false)
		{
			list($sqlState, $errCode, $errDescription) = $pdo->errorInfo();
			throw new kCoreException("Invalid sphinx query [$sql]\nSQLSTATE error code [$sqlState]\nDriver error code [$errCode]\nDriver error message [$errDescription]", APIErrors::SEARCH_ENGINE_QUERY_FAILED);
		}
		
		$idsCount = count($ids);
		$ids = $this->applyIds($ids);
		$this->setFetchedIds($ids);
		KalturaLog::debug("Found $idsCount ids");
		
		foreach($this->keyToRemove as $key)
		{
			KalturaLog::debug("Removing key [$key] from criteria");
			$this->remove($key);
		}
		
		$this->addAnd($this->getPropelIdField(), $ids, Criteria::IN);
		
		$this->recordsCount = 0;
		
		if(!$this->doCount)
			return;
			
		if($setLimit)
		{
			if ($this->getLimit() && $idsCount && $idsCount < $this->getLimit())
			{
				$this->recordsCount = $idsCount;
				if($this->getOffset())
					$this->recordsCount += $this->getOffset();				
			}
			else
			{
				$metaItems = $pdo->queryAndFetchAll("show meta like '%total_found%'", PDO::FETCH_ASSOC);
				if ($metaItems)
				{
					$metaItem = reset($metaItems);
					$this->recordsCount = (int)$metaItem['Value'];
					KalturaLog::debug('Sphinx query total_found: ' . $this->recordsCount);
				}
			}
			
			$this->setOffset(0);
		}
		else
		{
			$c = clone $this;
			$c->setLimit(null);
			$c->setOffset(null);
			$this->recordsCount = $this->doCountOnPeer($c);
		}
	}
	
	/**
	 * This function is responsible to sort the fields by their priority.
	 * Fields that cut more results, should be first so the query will be faster. 
	 */
	protected static function sortFieldsByPriority($fieldA,$fieldB) 
	{
		if($fieldA == $fieldB)
			return 0;
		
		$fieldsWithPriorities = kConf::get("fields_with_priorities_in_sphinx");
		
		$priorityA = 0;
		$priorityB = 0;
		
		$aFieldName = substr($fieldA,strpos($fieldA,".") + 1);
		$bFieldName = substr($fieldB,strpos($fieldB,".") + 1);
		
		if(array_key_exists($aFieldName, $fieldsWithPriorities)) 
			$priorityA = $fieldsWithPriorities[$aFieldName];
		
		if(array_key_exists($bFieldName, $fieldsWithPriorities))
			$priorityB = $fieldsWithPriorities[$bFieldName];
		
		return ($priorityB - $priorityA);
	}
	
	
	/**
	 * This function gets as input an array of arrays, and returns Cartesian product 
	 * (all possible conmbinations between the different arrays)
	 * For example, if the input is {(1,2,3) , (a,b) , (Apple)}
	 * the output will be {(1,a,Apple), (1,b,Apple),(2,a,Apple), (2,b,Apple),(3,a,Apple), (3,b,Apple)}
	 * 
	 * @param array $input
	 */
	protected static function cartesian($input) {
		$result = array();
	
		while (list($key, $values) = each($input)) {
			// If a sub-array is empty, it doesn't affect the cartesian product
			if (empty($values)) {
				continue;
			}
	
			// Special case: seeding the product array with the values from the first sub-array
			if (empty($result)) {
				foreach($values as $value) {
					$result[] = array($key => $value);
				}
			}
			else {
				// Second and subsequent input sub-arrays work like this:
				//   1. In each existing array inside $product, add an item with
				//      key == $key and value == first item in input sub-array
				//   2. Then, for each remaining item in current input sub-array,
				//      add a copy of each existing array inside $product with
				//      key == $key and value == first item in current input sub-array
	
				// Store all items to be added to $product here; adding them on the spot
				// inside the foreach will result in an infinite loop
				$append = array();
				foreach($result as &$product) {
					// Do step 1 above. array_shift is not the most efficient, but it
					// allows us to iterate over the rest of the items with a simple
					// foreach, making the code short and familiar.
					$product[$key] = array_shift($values);
	
					// $product is by reference (that's why the key we added above
					// will appear in the end result), so make a copy of it here
					$copy = $product;
	
					// Do step 2 above.
					foreach($values as $item) {
						$copy[$key] = $item;
						$append[] = $copy;
					}
	
					// Undo the side effecst of array_shift
					array_unshift($values, $product[$key]);
				}
	
				// Out of the foreach, we can add to $results now
				$result = array_merge($result, $append);
			}
		}
	
		return $result;
	}
	
	protected function getFieldPossibleValues(array $criterionsMap, $fieldName) {
		if(!array_key_exists($fieldName, $criterionsMap))
			return null;
		
		$criterion = $criterionsMap[$fieldName];
		return $criterion->getPossibleValues();
	}
	
	protected function addSphinxOptimizationMatches(array $criterionsMap) 
	{
		$optimizationField = $this->getSphinxOptimizationMap();
		
		foreach($optimizationField as $formatParams) {
			$hasEmptryField = false;
			$values = array();
			
			// Get values
			for($i = 1 ; $i < count($formatParams) ; $i++) {
				$value = $this->getFieldPossibleValues($criterionsMap,  $formatParams[$i]);
				if($value === null) {
					$hasEmptryField = true;
					break;
				}
				$values[] = $value;
			}
			
			if($hasEmptryField)
				continue;
			
			// Generate all possible combinations 
			$cartesianOptions = $this::cartesian($values);
			$format = $formatParams[0];
			$formatedStr = array();
			foreach ($cartesianOptions as $option ) {
				$curValue = trim ( vsprintf ( $format, $option ) );
				if (! $curValue)
					continue;
				$formatedStr[] = $curValue;
			}
			
			// Add condition
			if(empty($formatedStr) || (count($formatedStr) > SphinxCriterion::MAX_IN_VALUES))
				continue;
			
			$this->matchClause[] = "( @sphinx_match_optimizations " . implode(" | ", $formatedStr) . ")";
		}
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria#applyFilters()
	 */
	public function applyFilters()
	{
		if (KalturaLog::getEnableTests())
			KalturaLog::debug('kaltura_entry_criteria ' . serialize($this));
			
		$this->criteriasLeft = 0;
		
		KalturaLog::debug("Applies " . count($this->filters) . " filters");
		
		
		foreach($this->filters as $index => $filter)
		{
			KalturaLog::debug("Applies filter $index");
			$this->applyFilter(clone $filter);
		}
		
		// attach all default criteria from peer
		$this->getDefaultCriteriaFilter()->applyFilter($this);
		
		if(!$this->hasAdvancedSearchFilter && !count($this->matchClause) && $this->shouldSkipSphinx())
		{
			KalturaLog::debug('Skip Sphinx');
			$this->sphinxSkipped = true;
			return;
		}
		
		$fieldsToKeep = $this->getSphinxConditionsToKeep();
		$criterionsMap = $this->getMap();
		uksort($criterionsMap, array('SphinxCriteria','sortFieldsByPriority'));
		// go over all criterions and try to move them to the sphinx
		foreach($criterionsMap as $field => $criterion)
		{
			if(!($criterion instanceof SphinxCriterion))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] is not sphinx criteria");
				$this->criteriasLeft++;
				continue;
			}
			
			if($criterion->apply($this))
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] attached");
				if(!in_array($field, $fieldsToKeep))
					$this->keyToRemove[] = $field;
			}
			else
			{
				KalturaLog::debug("Criterion [" . $criterion->getColumn() . "] failed");
				$this->criteriasLeft++;
			}
		}
		
		KalturaLog::debug("Applied " . count($this->matchClause) . " matches, " . count($this->whereClause) . " clauses, " . count($this->keyToRemove) . " keys removed, $this->criteriasLeft keys left");
		
		// Adds special sphinx optimizations matches
		$this->addSphinxOptimizationMatches($criterionsMap);
		
		if(count($this->matchClause))
		{
			$this->matchClause = array_unique($this->matchClause);
			$matches = reset($this->matchClause);
			if(count($this->matchClause) > 1)
				$matches = '( ' . implode(' ) ( ', $this->matchClause) . ' )';
				
			$this->addWhere("MATCH('$matches')");
		}
		
		$conditions = '';
		$i = 0;
		foreach ($this->conditionClause as $conditionClause)
		{
			if ($this->conditionClause[$i] == '')
				continue;
			
			$conditions .=	', (' . $this->conditionClause[$i] . ') as cnd' . $i . ' ';
			$this->addWhere('cnd' . $i . ' > 0');
			
			$i++; 
		}
		
		$wheres = '';
		KalturaLog::debug("Where clause: " . print_r($this->whereClause, true));
		$this->whereClause = array_unique($this->whereClause);
		if(count($this->whereClause))
			$wheres = 'WHERE ' . implode(' AND ', $this->whereClause);

		$orderBy = '';
		$orderByColumns = $this->getOrderByColumns();
		$orderByColumns = array_unique($orderByColumns);
		
		$usesWeight = false;
		$setLimit = true;
		$orders = array();
		if(count($orderByColumns))
		{
			$replace = $this->getSphinxOrderFields();
			$search = array_keys($replace);
			
			
			$this->clearOrderByColumns();
			foreach($orderByColumns as $orderByColumn)
			{
				$arr = explode(' ', $orderByColumn);
				$orderField = $arr[0];
				
				if(isset($replace[$orderField]))
				{
					if($replace[$orderField] == self::WEIGHT) {
						$replace[$orderField] = "w";
						$conditions .= ",weight() as w";
						$usesWeight = true;
					}
					
					KalturaLog::debug("Add sort field[$orderField] copy from [$orderByColumn]");
					$orders[] = str_replace($search, $replace, $orderByColumn);
				}
				else
				{
					KalturaLog::debug("Skip sort field[$orderField] from [$orderByColumn] limit won't be used in sphinx query");
					$setLimit = false;
					$matches = null;
					if(preg_match('/^\s*([^\s]+)\s+(ASC|DESC)\s*$/i', $orderByColumn, $matches))
					{
						list($match, $column, $direction) = $matches;
						
						if(strtoupper($direction) == Criteria::DESC)
							$this->addDescendingOrderByColumn($column);
						else
							$this->addAscendingOrderByColumn($column);
					}
				}
			}
		}
		
		foreach ($this->orderByClause as $orderByClause)
		{
			$orders[] = $orderByClause;
		}
		
		if(count($orders))
		{			
			$this->applySortRequired = true;
			$orders = array_unique($orders);
			$orderBy = 'ORDER BY ' . implode(',', $orders);
		}
		else
		{
			$this->applySortRequired = false;
		}
		
		$this->ranker = self::RANKER_NONE;
		if ($usesWeight)
		{
			$this->ranker = self::RANKER_BM25;
		}
		
		$index = $this->getSphinxIndexName();
		$maxMatches = self::getMaxRecords();
		$limit = $maxMatches;
		
		if($this->criteriasLeft)
			$setLimit = false;
			
		if($setLimit && $this->getLimit())
		{
			if($this->getOffset() >= self::MAX_MATCHES)
			{
				throw new kCoreException("sphinx max matches limit was reached", kCoreException::SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED);
			}
			$maxMatches = min($maxMatches, $this->getLimit());
			$maxMatches += $this->getOffset();
			$maxMatches = min($maxMatches, self::MAX_MATCHES);
			$limit = $this->getLimit();
			if($this->getOffset())
				$limit = $this->getOffset() . ", $limit";
		}
		
		$this->executeSphinx($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit, $conditions);
	}
	
	/**
	 * Applies all filter fields and unset the handled fields
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		foreach($filter->fields as $field => $val)
		{
			if(is_null($val) || $val === '' || $field == '_order_by') 
				continue;
			
			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			if(count($fieldParts) != 3)
			{
				KalturaLog::debug("Skip field[$field] has [" . count($fieldParts) . "] parts");
				continue;
			}
			
			list($prefix, $operator, $fieldName) = $fieldParts;
			
			$fieldNamesArr = explode(baseObjectFilter::OR_SEPARATOR, $fieldName);
			if(count($fieldNamesArr) > 1)
			{
				$sphinxFieldNames = array();
				foreach($fieldNamesArr as $fieldName)
				{
					$sphinxField = $this->getSphinxFieldName($fieldName);
					$type = $this->getSphinxFieldType($sphinxField);
					$sphinxFieldNames[] = $sphinxField;
				}
				$sphinxField = '(' . implode(',', $sphinxFieldNames) . ')';
				$vals = is_array($val) ? $val : array_unique(explode(baseObjectFilter::OR_SEPARATOR, $val));
				$val = implode(' ', $vals);
			}
			elseif(!$this->hasMatchableField($fieldName))
			{
				KalturaLog::debug("Skip field[$field] has no matchable for name[$fieldName]");
				continue;
			}
			else
			{
				$sphinxField = $this->getSphinxFieldName($fieldName);
				$type = $this->getSphinxFieldType($sphinxField);
			}
			
			$valStr = print_r($val, true);
			$fieldsEscapeType = $this->getSearchIndexFieldsEscapeType($fieldName);
			
			KalturaLog::debug("Attach field[$fieldName] as sphinx field[$sphinxField] of type [$type] and comparison[$operator] for value[$valStr]");

			$partnerId = kCurrentContext::getCurrentPartnerId();
			$notEmpty = kSphinxSearchManager::HAS_VALUE . $partnerId;
			
			switch($operator)
			{
				case baseObjectFilter::MULTI_LIKE_OR:
				case baseObjectFilter::MATCH_OR:
					$vals = is_array($val) ? $val : explode(',', $val);
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
						$this->addMatch("@$sphinxField $val");
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::NOT_IN:
					$vals = is_array($val) ? $val : explode(',', $val);
						
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
						$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
						$val = $this->getFieldPrefix($sphinxField) . ' !' . implode(' !', $vals);
						$this->addMatch("@$sphinxField $val");
						$filter->unsetByName($field);
					}
					break;
				
				case baseObjectFilter::IN:
					$vals = is_array($val) ? $val : explode(',', $val);
						
					foreach($vals as $valIndex => $valValue)
					{
						if(!strlen($valValue))
							unset($vals[$valIndex]);
						else
							$vals[$valIndex] = SphinxUtils::escapeString($valValue, $fieldsEscapeType);
					}
					
					if(count($vals))
					{
						$vals = array_slice($vals, 0, SphinxCriterion::MAX_IN_VALUES);
						if($this->isNullableField($fieldName))
							$val = "((\\\"^" . implode(" $notEmpty$\\\") | (\\\"^", $vals) . " $notEmpty$\\\"))";
						else
							$val = "((\\\"^" . implode("$\\\") | (\\\"^", $vals) . "$\\\"))";
							
						$this->addMatch("@$sphinxField $val");
						$filter->unsetByName($field);
					}
					break;
					
				case baseObjectFilter::EQ:
					if(is_numeric($val) || strlen($val) > 0)
					{
						$val = SphinxUtils::escapeString($val, $fieldsEscapeType);	
						if($this->isNullableField($fieldName))
							$this->addMatch("@$sphinxField \\\"^$val $notEmpty$\\\"");
						else							
							$this->addMatch("@$sphinxField \\\"^$val$\\\"");
						$filter->unsetByName($field);
					}
					break;
					
				case baseObjectFilter::IS_EMPTY:
					if($val)
					{
						$isEmpty = kSphinxSearchManager::HAS_NO_VALUE . $partnerId;
						$this->addMatch("@$sphinxField $isEmpty");
					}
					else 
					{
						$this->addMatch("@$sphinxField $notEmpty");
					}
					$filter->unsetByName($field);
					break;
				
				case baseObjectFilter::MULTI_LIKE_AND:
				case baseObjectFilter::MATCH_AND:
					$vals = is_array($val) ? $val : explode(',', $val);
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
						$this->addMatch("@$sphinxField $val");
						$filter->unsetByName($field);
					}
					break;		
								
				case baseObjectFilter::LIKE:
					if(strlen($val))
					{
						if(preg_match('/[\s\t]/', $val)) //if there are spaces or tabs - should add "<VALUE>"
							$val = '"' . SphinxUtils::escapeString($val, $fieldsEscapeType) . '"';
						else
							$val = SphinxUtils::escapeString($val, $fieldsEscapeType);
						
						$this->addMatch("@$sphinxField $val");
						$filter->unsetByName($field);
					}
					break;
					
				case baseObjectFilter::LIKEX:
			        if(strlen($val))
					{
						$val = SphinxUtils::escapeString($val, $fieldsEscapeType);
						if ($fieldsEscapeType != SearchIndexFieldEscapeType::MD5_LOWER_CASE)
						{
							$this->addMatch('@' .  $sphinxField . ' "' .$val . '\\\*"');
						}
						else
						{
							$this->addMatch('@' .  $sphinxField . ' "'.$val.'"');
						}
						$filter->unsetByName($field);
					}
				    break;	
				case baseObjectFilter::NOT_CONTAINS:
					$val = is_array($val) ? $val : explode(",", $val);
					foreach ($val as &$singleVal)
					{
						$singleVal = SphinxUtils::escapeString($singleVal, $fieldsEscapeType);
					}
					if ($this->getFieldPrefix ($sphinxField))
					{
						$this->addMatch('@' .  $sphinxField .  ' ' . $this->getFieldPrefix ($sphinxField) . ' -(' .implode(' | ', $val) . ')');
					}
					$filter->unsetByName($field);
					break;
				default:
					KalturaLog::debug("Skip field[$field] has no opertaor[$operator]");
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCriteria::applyFilter()
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
		
		$this->applyFilterFields($filter);
		
		// attach all unhandled fields
		$filter->attachToFinalCriteria($this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KalturaCriteria::applyResultsSort()
	 */
	public function applyResultsSort(array &$objects)
	{
		if (!$this->applySortRequired)
			return;
		
		$sortedResult = array();
		
		foreach ($objects as $object)
			$sortedResult[$object->getId()] = $object; 
		
		$objects = array();
		foreach ($this->fetchedIds as $fetchedId)
			if(isset($sortedResult[$fetchedId]))
				$objects[] = $sortedResult[$fetchedId];
	}
	
	/* (non-PHPdoc)
	 * @see Criteria::getNewCriterion()
	 */
	public function getNewCriterion($column, $value = null, $comparison = self::EQUAL)
	{
		return new SphinxCriterion($this, $column, $value, $comparison);
	}
	
	static private function addCriterionField(&$criterionFields, $field, $comparisons)
	{
		if (!isset($criterionFields[$field]))
			$criterionFields[$field] = array();
		$criterionFields[$field] = array_unique(array_merge($criterionFields[$field], $comparisons));
	}
	
	static private function getAllCriterionFields($criterions)
	{
		$criterionFields = array();
		
		foreach ($criterions as $criterion)
		{
			if(!($criterion instanceof SphinxCriterion))
				continue;
		
			self::addCriterionField($criterionFields, $criterion->getTable() . '.' . $criterion->getColumn(), array($criterion->getComparison()));
				
			$clausesFields = self::getAllCriterionFields($criterion->getClauses());			
			foreach ($clausesFields as $field => $fieldComparisons)
			{
				self::addCriterionField($criterionFields, $field, $fieldComparisons);
			}
		}
		
		return $criterionFields;
	}
	
	/**
	 * @return array
	 */
	public function getSphinxOptimizationMap() {
		return array();
	}
	
	/**
	 * This function returns a list of fields that indicates whether the query should skip sphinx and go
	 * directly to the database. For example, if a query on 'entry' contains entry.ID IN (...) going
	 * through sphinx does not help (unless there is some textual match as well). In this case the function
	 * should include entry.ID in the returned array.
	 * @return array
	 */
	public function getSkipFields()
	{
		return array();
	}
	
	/**
	 * This function returns a list of fields that indicates whether the query should keep the sphinx qeury condition
	 * but use it as well when querying from the database. For example, if a query on 'entry' contains entry.partner_id,
	 * we'd like to use the same condition on the database as well.
	 * 
	 * @return array
	 */
	public function getSphinxConditionsToKeep() {
		return array();
	}
		
	private function shouldSkipSphinx()
	{
		$skipFields = $this->getSkipFields();
		
		$hasSkipField = false;
		foreach ($skipFields as $skipField)
		{
			$skipCrit = $this->getCriterion($skipField);
			if($skipCrit && in_array($skipCrit->getComparison(), array(Criteria::EQUAL, Criteria::IN)))
			{
				$hasSkipField = true;
				break;
			}
		}
		
		if (!$hasSkipField)
			return false;
		
		$fields = self::getAllCriterionFields($this->getMap());
		
		foreach ($this->getOrderByColumns() as $orderByColumn)
		{
			// strip asc / desc
			$orderByColumn = str_replace(' ASC', '', str_replace(' DESC', '', $orderByColumn));
			
			// strip ()'s
			if (preg_match('/^\(.*\)$/', $orderByColumn))
				$orderByColumn = substr($orderByColumn, 1, -1);
				
			// strip <> operator (for PARTNER_ID<>1234 order by added by addPartnerToCriteria)
			$explodedColumn = explode('<>', $orderByColumn);
			$orderByColumn = $explodedColumn[0];
			
			self::addCriterionField($fields, $orderByColumn, array(Criteria::GREATER_THAN));		// treat the order by comparison as >
		}
								
		foreach($fields as $field => $comparisons)
		{	
			$fieldName = $this->getSphinxFieldName($field);

			if(in_array($field, $skipFields))
			{
				continue;
			}
			elseif(!$this->hasPeerFieldName($field))
			{
				KalturaLog::debug('Peer does not have the field [' . print_r($field,true) .']');
				return false;
			}
			elseif($this->getSphinxFieldType($fieldName) == IIndexable::FIELD_TYPE_STRING && 
					array_diff($comparisons, array(Criteria::EQUAL, Criteria::IN)))
			{
				KalturaLog::debug('Field is textual [' . print_r($fieldName,true) .'] and using comparisons [' . implode(',', $comparisons), ']');
				return false;
			}
		}

		return true;
	}
	
	public function hasPeerFieldName($field)
	{
		return false;
	}

	/**
	 * @return int $recordsCount
	 */
	public function getRecordsCount() 
	{
		if (!$this->sphinxSkipped)
			return $this->recordsCount;
		
		$c = clone $this;
		$c->setLimit(null);
		$c->setOffset(null);
		$this->recordsCount = $this->doCountOnPeer($c);
		
		$this->sphinxSkipped = false;

		return $this->recordsCount;
		
	}
	
	public function getTranslateIndexId($id)
	{
		return sprintf('%u', crc32($id));
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addWhere()
	 */
	public function addWhere($statement)
	{
		if(strlen(trim($statement)))
		{
			$this->whereClause[] = $statement;
			KalturaLog::debug("Added [$statement] count [" . count($this->whereClause) . "]");
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addMatch()
	 */
	public function addMatch($match)
	{
		if(strlen(trim($match)))
		{
			$this->matchClause[] = $match;
			KalturaLog::debug("Added [$match] count [" . count($this->matchClause) . "]");
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addCondition()
	 */
	public function addCondition($condition)
	{
		if(strlen(trim($condition)))
		{
			KalturaLog::debug("Added [$condition]");
			$this->conditionClause[] = $condition;
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addOrderBy()
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC)
	{
		if(strlen(trim($column)))
		{
			KalturaLog::debug("Added [$column]");
			$this->orderByClause[] = "$column $orderByType";
		}
	}
	
	public function getFieldPrefix ($fieldName)
	{
		return null;	
	}

	public function translateSphinxCriterion(SphinxCriterion $crit)
	{
		return array(
				$crit->getTable() . '.' . $crit->getColumn(),
				$crit->getComparison(),
				$crit->getValue());
	}
	

	public function setSelectColumn($name)
	{
		$sphinxColumnName = $this->getSphinxFieldName($name);
		$this->selectColumn = $sphinxColumnName;
	}
	
	public function setGroupByColumn ($name)
	{
		$sphinxColumnName = $this->getSphinxFieldName($name);
		$this->groupByColumn = $sphinxColumnName;
	}
}
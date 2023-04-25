<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.filters
 */
class ESearchHistoryFilter extends ESearchBaseFilter
{

	const DEFAULT_SUGGEST_SIZE = 5;
	const STARTS_WITH_PAGE_SIZE = 100;
	const DEFAULT_LIST_SIZE = 500;
	const MAX_SEARCH_TERM_LENGTH = 64;
	const MAX_AGGREGATION_SIZE = 100;
	const LIMIT_IN_MONTHS = 3;
	const SECONDS_IN_DAY = 86400;
	const MAX_DAYS_IN_MONTH = 31;


	const SUGGEST_SEARCH_HISTORY = 'suggest_search_history';

	/**
	 * @var string
	 */
	protected $searchTermStartsWith;

	/**
	 * @var string
	 */
	protected $searchedObjectIn;

	/**
	 * @var ESearchRange
	 */
	protected $timestampRange;

	/**
	 * @var ESearchAggregationItem
	 */
	protected $aggregation;

	/**
	 * @return string
	 */
	public function getSearchTermStartsWith()
	{
		return $this->searchTermStartsWith;
	}

	/**
	 * @param string $searchTermStartsWith
	 */
	public function setSearchTermStartsWith($searchTermStartsWith)
	{
		$this->searchTermStartsWith = $searchTermStartsWith;
	}

	/**
	 * @return string
	 */
	public function getSearchedObjectIn()
	{
		return $this->searchedObjectIn;
	}

	/**
	 * @param string $searchedObjectIn
	 */
	public function setSearchedObjectIn($searchedObjectIn)
	{
		$this->searchedObjectIn = $searchedObjectIn;
	}

	/**
	 * @return ESearchRange
	 */
	public function getTimestampRange()
	{
		return $this->timestampRange;
	}

	/**
	 * @param ESearchRange $timestampRange
	 */
	public function setTimestampRange($timestampRange)
	{
		$this->timestampRange = $timestampRange;
	}

	/**
	 * @return ESearchAggregationItem
	 */
	public function getAggregation()
	{
		return $this->aggregation;
	}

	/**
	 * @param ESearchAggregationItem $aggregation
	 */
	public function setAggregation($aggregation)
	{
		$this->aggregation = $aggregation;
	}

	public function execQueryFromFilter()
	{
		$this->applyFilter();
		$historyClient = new kESearchHistoryElasticClient();
		$elasticResults = $historyClient->searchRecentForUser($this->query);
		return kESearchHistoryCoreAdapter::getCoreESearchHistoryFromResults($elasticResults);
	}

	protected function applyFilter()
	{
		if ($this->getAggregation())
		{
			$this->applyFilterForAggregation();
		}
		else
		{
			$this->applyFilterForQuery();
		}
	}

	protected function applyFilterForQuery()
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if (!$kuserId)
		{
			throw new kESearchHistoryException('Invalid userId', kESearchHistoryException::INVALID_USER_ID);
		}

		$boolQuery = new kESearchBoolQuery();
		$pidUidContext = searchHistoryUtils::formatPartnerIdUserIdContext($partnerId, $kuserId, searchHistoryUtils::getSearchContext());
		$pidUidContextQuery = new kESearchTermQuery(ESearchHistoryFieldName::PID_UID_CONTEXT, $pidUidContext);
		$boolQuery->addToFilter($pidUidContextQuery);

		$this->applyFilterFields($boolQuery);

		$this->query[kESearchQueryManager::SORT_KEY] = array(ESearchHistoryFieldName::TIMESTAMP => array(kESearchQueryManager::ORDER_KEY => ESearchSortOrder::ORDER_BY_DESC));
		$this->query[kESearchQueryManager::FROM_KEY] = 0;
		$this->query[kESearchQueryManager::SIZE_KEY] = $this->getQueryPageSize();
	}

	protected function applyFilterForAggregation()
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$boolQuery = new kESearchBoolQuery();
		$partnerIdQuery = new kESearchTermQuery(ESearchHistoryFieldName::PARTNER_ID, $partnerId);
		$boolQuery->addToFilter($partnerIdQuery);

		$this->applyFilterFields($boolQuery, true);

		$aggregationKey = ESearchHistoryAggregationItem::KEY . ':' . $this->aggregation->getFieldName();
		$this->aggregation->setSize($this->getAggregationSize());
		$aggregations[$aggregationKey] = $this->aggregation->getAggregationCommand();

		$this->query[ESearchAggregations::AGGS] = $aggregations;

		//query size is 0 because we don't want to return hits, only aggregation results
		$this->query[kESearchQueryManager::SIZE_KEY] = 0;
	}

	protected function applyFilterFields($boolQuery, $limitTimestamp=false)
	{
		$searchTermStartsWith = $this->getSearchTermStartsWith();
		if ($searchTermStartsWith)
		{
			if (strlen($searchTermStartsWith) > self::MAX_SEARCH_TERM_LENGTH)
			{
				$searchTermStartsWith = mb_strcut($searchTermStartsWith, 0, self::MAX_SEARCH_TERM_LENGTH, "utf-8");
			}
			$searchTermStartsWithQuery = new kESearchPrefixQuery(ESearchHistoryFieldName::SEARCH_TERM, elasticSearchUtils::formatSearchTerm($searchTermStartsWith));
			$boolQuery->addToFilter($searchTermStartsWithQuery);
		}

		if ($this->searchedObjectIn)
		{
			$searchedObjects = $this->getSearchedObjectsArray();
			$searchObjectsQuery = new kESearchTermsQuery(ESearchHistoryFieldName::SEARCHED_OBJECT, $searchedObjects);
			$boolQuery->addToFilter($searchObjectsQuery);
		}

		if ($limitTimestamp)
		{
			$this->validateTimestampLimit();
		}

		$timestampRange = $this->getTimestampRange();
		if ($timestampRange)
		{
			$rangeQuery = new kESearchRangeQuery($timestampRange, ESearchHistoryFieldName::TIMESTAMP);
			$boolQuery->addToFilter($rangeQuery);
		}

		$this->query[kESearchQueryManager::QUERY_KEY] = $boolQuery->getFinalQuery();

	}

	protected function validateTimestampLimit()
	{
		if (!$this->getTimestampRange())
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "timestampRange");
		}

		$limitInMonths = $this->getAggregationTimestampLimit();
		$endTimestamp = $this->getEndTimestamp();
		$startTimestamp = $this->getStartTimestamp();

		if ($startTimestamp)
		{
			if ((int)(($endTimestamp - $startTimestamp) / self::SECONDS_IN_DAY) > $limitInMonths * self::MAX_DAYS_IN_MONTH)
			{
				throw new KalturaAPIException(KalturaESearchHistoryErrors::TIME_RANGE_EXCEEDED_LIMIT, $limitInMonths);
			}
		}
		else
		{
			throw new KalturaAPIException(KalturaESearchHistoryErrors::TIME_RANGE_EXCEEDED_LIMIT, $limitInMonths);
		}
	}

	protected function getEndTimestamp()
	{
		$timestampRange = $this->getTimestampRange();
		$lessThanOrEqual = $timestampRange->getLessThanOrEqual();
		$lessThan = $timestampRange->getLessThan();
		if ($lessThanOrEqual)
		{
			return $lessThan ? min($lessThan, $lessThanOrEqual) : $lessThanOrEqual;
		}
		else
		{
			return $lessThan ? $lessThan : time();
		}
	}

	protected function getStartTimestamp()
	{
		$timestampRange = $this->getTimestampRange();
		return max($timestampRange->getGreaterThanOrEqual(), $timestampRange->getGreaterThan());
	}

	protected function getQueryPageSize()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		if ($this->getSearchTermStartsWith())
		{
			return isset($searchHistoryConfig['completionListSize']) ? $searchHistoryConfig['completionListSize'] : self::STARTS_WITH_PAGE_SIZE;
		}
		else
		{
			return isset($searchHistoryConfig['emptyTermListSize']) ? $searchHistoryConfig['emptyTermListSize'] : self::DEFAULT_LIST_SIZE;
		}
	}

	protected function getAggregationSize()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		$aggregationSize = isset($searchHistoryConfig['aggregationSize']) ? $searchHistoryConfig['aggregationSize'] : self::MAX_AGGREGATION_SIZE;
		return min($this->aggregation->getSize(), $aggregationSize);
	}

	protected function getAggregationTimestampLimit()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		return isset($searchHistoryConfig['aggregationRangeInMonths']) ? $searchHistoryConfig['aggregationRangeInMonths'] : self::LIMIT_IN_MONTHS;
	}

	protected function getSearchedObjectsArray()
	{
		$searchedObjects = array();
		$searchedObjectsArr = explode(',', $this->getSearchedObjectIn());

		foreach ($searchedObjectsArr as $searchObject)
		{
			$searchedObjects[] = trim($searchObject);
		}
		return $searchedObjects;
	}

}

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

	public function execQueryFromFilter()
	{
		$this->applyFilter();
		$historyClient = new kESearchHistoryElasticClient();
		$elasticResults = $historyClient->searchRecentForUser($this->query);
		return kESearchHistoryCoreAdapter::getCoreESearchHistoryFromResults($elasticResults);
	}

	protected function applyFilter()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if (!$kuserId)
		{
			throw new kESearchHistoryException('Invalid userId', kESearchHistoryException::INVALID_USER_ID);
		}
		$pageSize = isset($searchHistoryConfig['emptyTermListSize']) ? $searchHistoryConfig['emptyTermListSize'] : self::DEFAULT_LIST_SIZE;

		$boolQuery = new kESearchBoolQuery();
		$pidUidContext = searchHistoryUtils::formatPartnerIdUserIdContext($partnerId, $kuserId, searchHistoryUtils::getSearchContext());
		$pidUidContextQuery = new kESearchTermQuery(ESearchHistoryFieldName::PID_UID_CONTEXT, $pidUidContext);
		$boolQuery->addToFilter($pidUidContextQuery);
		if ($this->getSearchTermStartsWith())
		{
			$searchTermStartsWithQuery = new kESearchPrefixQuery(ESearchHistoryFieldName::SEARCH_TERM, elasticSearchUtils::formatSearchTerm($this->getSearchTermStartsWith()));
			$boolQuery->addToFilter($searchTermStartsWithQuery);
			$pageSize = isset($searchHistoryConfig['completionListSize']) ? $searchHistoryConfig['completionListSize'] : self::STARTS_WITH_PAGE_SIZE;
		}
		if($this->searchedObjectIn)
		{
			$searchedObjects = $this->getSearchedObjectsArray();
			$searchObjectsQuery = new kESearchTermsQuery(ESearchHistoryFieldName::SEARCHED_OBJECT, $searchedObjects);
			$boolQuery->addToFilter($searchObjectsQuery);
		}

		$this->query[kESearchQueryManager::QUERY_KEY] = $boolQuery->getFinalQuery();
		$this->query[kESearchQueryManager::SORT_KEY] = array(ESearchHistoryFieldName::TIMESTAMP => array(kESearchQueryManager::ORDER_KEY => ESearchSortOrder::ORDER_BY_DESC));
		$this->query[kESearchQueryManager::FROM_KEY] = 0;
		$this->query[kESearchQueryManager::SIZE_KEY] = $pageSize;
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

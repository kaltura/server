<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.filters
 */
class ESearchHistoryFilter extends ESearchBaseFilter
{

	const DEFAULT_SUGGEST_SIZE = 5;
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

		if ($this->getSearchTermStartsWith())
		{
			$suggest = new kESearchContextCompletionSuggestQuery();
			$suggest->setPrefix(elasticSearchUtils::formatSearchTerm($this->searchTermStartsWith));
			$suggest->setField(ESearchHistoryFieldName::SEARCH_TERM_COMPLETION);
			$completionSize = isset($searchHistoryConfig['completionSize']) ? $searchHistoryConfig['completionSize'] : self::DEFAULT_SUGGEST_SIZE;
			$suggest->setSize($completionSize);
			$suggest->setSkipDuplicates(true);
			$suggest->setSuggestName(self::SUGGEST_SEARCH_HISTORY);
			$this->addContextsToSuggester($suggest, $partnerId, $kuserId);
			$this->query = $suggest->getFinalQuery();
		}
		else
		{
			$boolQuery = new kESearchBoolQuery();
			$partnerTerm = new kESearchTermQuery(ESearchHistoryFieldName::PARTNER_ID, $partnerId);
			$boolQuery->addToFilter($partnerTerm);
			$kuserTerm = new kESearchTermQuery(ESearchHistoryFieldName::KUSER_ID, $kuserId);
			$boolQuery->addToFilter($kuserTerm);
			$searchContextTerm = new kESearchTermQuery(ESearchHistoryFieldName::SEARCH_CONTEXT, searchHistoryUtils::getSearchContext());
			$boolQuery->addToFilter($searchContextTerm);
			if($this->searchedObjectIn)
			{
				$searchedObjects = $this->getSearchedObjectsArray();
				$searchObjectsQuery = new kESearchTermsQuery(ESearchHistoryFieldName::SEARCHED_OBJECT, $searchedObjects);
				$boolQuery->addToFilter($searchObjectsQuery);
			}

			$this->query[kESearchQueryManager::QUERY_KEY] = $boolQuery->getFinalQuery();
			$this->query[kESearchQueryManager::SORT_KEY] = array(ESearchHistoryFieldName::TIMESTAMP => array(kESearchQueryManager::ORDER_KEY => ESearchSortOrder::ORDER_BY_DESC));
			$this->query[kESearchQueryManager::FROM_KEY] = 0;
			$this->query[kESearchQueryManager::SIZE_KEY] = isset($searchHistoryConfig['emptyTermListSize']) ? $searchHistoryConfig['emptyTermListSize'] : self::DEFAULT_LIST_SIZE;
		}
	}

	protected function addContextsToSuggester(&$suggest, $partnerId, $kuserId)
	{
		$searchContext = searchHistoryUtils::getSearchContext();
		$monthBoostMap = searchHistoryUtils::getBoostMap();
		if ($this->getSearchedObjectIn())
		{
			$searchedObjects = $this->getSearchedObjectsArray();
			foreach ($searchedObjects as $searchedObject)
			{
				$this->addSearchContexts($suggest, $monthBoostMap, $partnerId, $kuserId, $searchContext, $searchedObject);
			}
		}
		else
		{
			$this->addSearchContexts($suggest, $monthBoostMap, $partnerId, $kuserId, $searchContext);
		}
	}

	protected function addSearchContexts(&$suggest, $monthBoostMap, $partnerId, $kuserId, $searchContext, $searchedObject = null)
	{
		foreach ($monthBoostMap as $month => $boost)
		{
			$context = new kESearchSuggestContext();
			$context->setName(ESearchHistoryFieldName::CONTEXT_CATEGORY);
			if ($searchedObject)
			{
				$value = searchHistoryUtils::formatMonthPartnerIdUserIdContextObject($month, $partnerId, $kuserId, $searchContext, $searchedObject);
			}
			else
			{
				$value = searchHistoryUtils::formatMonthPartnerIdUserIdContext($month, $partnerId, $kuserId, $searchContext);
			}
			$context->setValue(strtolower($value));
			$context->setBoost($boost);
			$suggest->addContext($context);
		}
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

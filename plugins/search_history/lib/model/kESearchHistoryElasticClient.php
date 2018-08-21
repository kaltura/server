<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class kESearchHistoryElasticClient
{

	const INDEX_KEY = 'index';
	const BODY_KEY = 'body';
	const QUERY_KEY = 'query';

	protected $client;

	public function __construct()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		if (!isset($searchHistoryConfig['elasticHost']) || !isset($searchHistoryConfig['elasticPort']))
		{
			throw new kESearchHistoryException('Missing mandatory config', kESearchHistoryException::INTERNAL_SERVER_ERROR);
		}
		$elasticHost = $searchHistoryConfig['elasticHost'];
		$elasticPort = $searchHistoryConfig['elasticPort'];
		$this->client = new elasticClient($elasticHost, $elasticPort);
	}

	public function deleteSearchTermForUser($searchTerm)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if (!$kuserId)
		{
			throw new kESearchHistoryException('Invalid userId', kESearchHistoryException::INVALID_USER_ID);
		}

		$deleteByQuery = new kESearchBoolQuery();
		$partnerTerm = new kESearchTermQuery(ESearchHistoryFieldName::PARTNER_ID, $partnerId);
		$deleteByQuery->addToFilter($partnerTerm);
		$kuserTerm = new kESearchTermQuery(ESearchHistoryFieldName::KUSER_ID, $kuserId);
		$deleteByQuery->addToFilter($kuserTerm);
		$searchContextTerm = new kESearchTermQuery(ESearchHistoryFieldName::SEARCH_CONTEXT, searchHistoryUtils::getSearchContext());
		$deleteByQuery->addToFilter($searchContextTerm);
		$searchTermQuery = new kESearchTermQuery(ESearchHistoryFieldName::SEARCH_TERM, elasticSearchUtils::formatSearchTerm($searchTerm));
		$deleteByQuery->addToFilter($searchTermQuery);
		$query = array(
			self::INDEX_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_INDEX,
			self::BODY_KEY => array(
				self::QUERY_KEY => $deleteByQuery->getFinalQuery()
			)
		);

		$this->client->deleteByQuery($query);
	}

	public function searchRecentForUser($queryBody)
	{
		$query = array(
			self::INDEX_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_INDEX,
			self::BODY_KEY => $queryBody
		);

		$result = $this->client->search($query, true);
		return $result;
	}

}

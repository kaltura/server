<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class kESearchHistoryElasticClient
{

	const INDEX_KEY = 'index';
	const TYPE_KEY = 'type';
	const BODY_KEY = 'body';
	const QUERY_KEY = 'query';
	const ACTION_KEY = '_action';
	const DELETE_KEY = 'delete';
	const IDS_TO_DELETE = 'ids_to_delete';
	const MAX_SEARCH_TERMS_TO_DELETE = 1000;

	protected $client;

	public function __construct()
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		
		// Values are set to 'null' to maintain backward compatibility for elasticClient() constructor
		$host = $searchHistoryConfig['elasticHost'] ?? null;
		$port = $searchHistoryConfig['elasticPort'] ?? null;
		$elasticVersion = $searchHistoryConfig['elasticVersion'] ?? null;
		
		$this->client = new elasticClient($host, $port, $elasticVersion);
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
			self::INDEX_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_SEARCH_ALIAS,
			self::TYPE_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_TYPE,
			self::BODY_KEY => array(
				kESearchQueryManager::FROM_KEY => 0,
				kESearchQueryManager::SIZE_KEY => self::MAX_SEARCH_TERMS_TO_DELETE,
				self::QUERY_KEY => $deleteByQuery->getFinalQuery()
			)
		);

		$result = $this->client->search($query, true);
		$ids = kESearchHistoryCoreAdapter::getIdsToDeleteFromHitsResults($result);
		if (!$ids)
			return;

		$body = array(
			self::ACTION_KEY => self::DELETE_KEY,
			'_'.self::TYPE_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_TYPE,
			self::IDS_TO_DELETE => $ids
		);
		$document = json_encode($body);
		try
		{
			$constructorArgs['exchangeName'] = kESearchHistoryManager::HISTORY_EXCHANGE_NAME;
			$queueProvider = QueueProvider::getInstance(null, $constructorArgs);
			$queueProvider->send(kESearchHistoryManager::HISTORY_QUEUE_NAME, $document);
		}
		catch (Exception $e)
		{
			//don't fail the request, just log
			KalturaLog::err("cannot connect to rabbit");
		}
	}

	public function searchRecentForUser($queryBody)
	{
		$query = array(
			self::INDEX_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_SEARCH_ALIAS,
			self::TYPE_KEY => ESearchHistoryIndexMap::SEARCH_HISTORY_TYPE,
			self::BODY_KEY => $queryBody
		);

		$result = $this->client->search($query, true);
		return $result;
	}

}

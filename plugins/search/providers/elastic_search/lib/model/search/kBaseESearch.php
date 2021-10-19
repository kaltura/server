<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

abstract class kBaseESearch extends kBaseSearch
{
	const GLOBAL_HIGHLIGHT_CONFIG = 'globalMaxNumberOfFragments';

	public abstract function getElasticTypeName();

	public abstract function fetchCoreObjectsByIds($ids);

	protected function execSearch(ESearchOperator $eSearchOperator)
	{
		$subQuery = $eSearchOperator::createSearchQuery($eSearchOperator->getSearchItems(), null, $this->queryAttributes, $eSearchOperator->getOperator());
		$this->handleDisplayInSearch();
		if($this->filterOnlyContext)
		{
			$this->mainBoolQuery->addToFilter($subQuery);
		}
		else
		{
			$this->mainBoolQuery->addToMust($subQuery);
		}
		$this->applyElasticSearchConditions();
		$this->addGlobalHighlights();
		$result = $this->elasticClient->search($this->query, true, true);
		$this->addSearchTermsToSearchHistory();
		return $result;
	}

	protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null)
	{
		$partnerId = kBaseElasticEntitlement::$partnerId;
		$this->initQueryAttributes($partnerId, $objectId);
		$this->initBaseFilter($partnerId, $statuses, $objectId);
		$this->initPager($pager);
		$this->initOrderBy($order);
		$this->initAggregations($aggregations);
	}

	protected function addGlobalHighlights()
	{
		$this->queryAttributes->getQueryHighlightsAttributes()->setScopeToGlobal();
		$numOfFragments = elasticSearchUtils::getNumOfFragmentsByConfigKey(self::GLOBAL_HIGHLIGHT_CONFIG);
		$highlight = new kESearchHighlightQuery($this->queryAttributes->getQueryHighlightsAttributes()->getFieldsToHighlight(), $numOfFragments);
		$highlight = $highlight->getFinalQuery();
		if($highlight)
		{
			$this->query['body']['highlight'] = $highlight;
		}
	}

	protected function addSearchTermsToSearchHistory()
	{
		$searchTerms = $this->queryAttributes->getSearchHistoryTerms();
		$searchTerms = array_unique($searchTerms);
		$searchTerms = array_values($searchTerms);
		if (!$searchTerms)
		{
			KalturaLog::log("Empty search terms, not adding to search history");
			return;
		}
		$partner = PartnerPeer::retrieveByPk(kCurrentContext::getCurrentPartnerId());
		if(!$partner || $partner->getAvoidIndexingSearchHistory())
		{
			KalturaLog::log("Partner does not support search history indexing");
			return;
		}

		$searchHistoryInfo = new ESearchSearchHistoryInfo();
		$searchHistoryInfo->setSearchTerms($searchTerms);
		$searchHistoryInfo->setPartnerId(kBaseElasticEntitlement::$partnerId);
		$searchHistoryInfo->setKUserId(kBaseElasticEntitlement::$kuserId);
		$searchHistoryInfo->setSearchContext(searchHistoryUtils::getSearchContext());
		$searchHistoryInfo->setSearchedObject($this->getElasticTypeName());
		$searchHistoryInfo->setTimestamp(time());
		kEventsManager::raiseEventDeferred(new kESearchSearchHistoryInfoEvent($searchHistoryInfo));
	}

	public static function getElasticIndexNamePerPartner($indexName, $partnerId, $useSplitIndexName = true)
	{
		//Legacy support of dedicated entry table
		if($indexName===ElasticIndexMap::ELASTIC_ENTRY_INDEX)
		{
			$dedicateEntryPartnerList = kConf::get(ElasticSearchPlugin::DEDICATED_ENTRY_INDEX_PARTNER_LIST,ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, array());
			if(in_array($partnerId,$dedicateEntryPartnerList))
			{
				$indexName = kConf::get(ElasticSearchPlugin::DEDICATED_ENTRY_INDEX_NAME,ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, $indexName);
			}
		}

		$dedicatedPartnersList = kConf::get(ElasticSearchPlugin::DEDICATE_INDEX_PARTNER_LIST, ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, array());

		if(isset($dedicatedPartnersList[$partnerId]))
		{
			$indices = explode(',', $dedicatedPartnersList[$partnerId]);
			foreach ($indices as $indexNameInConfig)
			{
				if($indexName === trim($indexNameInConfig))
				{
					$indexName .= '_' . $partnerId;
					break;
				}
			}
		}

		if($useSplitIndexName)
		{
			$indexName = self::getSplitIndexNamePerPartner($indexName, $partnerId);
		}

		KalturaLog::debug("Using index name $indexName for $partnerId");

		return $indexName;
	}

	public static function getSplitIndexNamePerPartner($indexName, $partnerId)
	{
		$clusterName = kConf::get('elasticCluster', 'elastic', null);
		if(!$clusterName)
		{
			KalturaLog::debug("Could not get elastic cluster name");
			return $indexName;
		}

		$elasticSplitIndexMap = kConf::get($clusterName,'elasticSplitIndexMap',array());
		if(!empty($elasticSplitIndexMap) && isset($elasticSplitIndexMap[$indexName]))
		{
			$splitFactor = $elasticSplitIndexMap[$indexName];
			$index = abs(($partnerId / 10) % $splitFactor);
			$indexName = $indexName .'_' . $index;
		}
		return $indexName;
	}
}

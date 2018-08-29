<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

abstract class kBaseSearch
{

	const GLOBAL_HIGHLIGHT_CONFIG = 'globalMaxNumberOfFragments';

    protected $elasticClient;
    protected $query;
    protected $queryAttributes;
	protected $mainBoolQuery;

    public function __construct()
    {
        $this->elasticClient = new elasticClient();
        $this->queryAttributes = new ESearchQueryAttributes();
		$this->mainBoolQuery = new kESearchBoolQuery();
    }

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null);

    public abstract function getPeerName();

	public abstract function getPeerRetrieveFunctionName();

    public abstract function getElasticTypeName();

	/**
	 * @return ESearchQueryAttributes
	 */
	public function getQueryAttributes()
	{
		return $this->queryAttributes;
	}

	protected function handleDisplayInSearch()
	{
	}

    protected function execSearch(ESearchOperator $eSearchOperator)
    {
        $subQuery = $eSearchOperator::createSearchQuery($eSearchOperator->getSearchItems(), null, $this->queryAttributes, $eSearchOperator->getOperator());
        $this->handleDisplayInSearch();
        $this->mainBoolQuery->addToMust($subQuery);
        $this->applyElasticSearchConditions();
        $this->addGlobalHighlights();
        $result = $this->elasticClient->search($this->query, true);
        $resultCount = isset($result[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::TOTAL_KEY]) ? $result[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::TOTAL_KEY] : 0;
        $this->addSearchTermsToSearchHistory($resultCount);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $partnerId = kBaseElasticEntitlement::$partnerId;
        $this->initQueryAttributes($partnerId, $objectId);
        $this->initBaseFilter($partnerId, $statuses, $objectId);
        $this->initPager($pager);
        $this->initOrderBy($order);
    }

    protected function initPager(kPager $pager = null)
    {
        if($pager)
        {
            $this->query['from'] = $pager->calcOffset();
            $this->query['size'] = $pager->calcPageSize();
        }
    }

    protected function initOrderBy(ESearchOrderBy $order = null)
    {
        if($order)
        {
            $orderItems = $order->getOrderItems();
            $fields = array();
            $sortConditions = array();
            foreach ($orderItems as $orderItem)
            {
                $field = $orderItem->getSortField();
                if(isset($fields[$field]))
                {
                    KalturaLog::log("Order by condition already set for field [$field]" );
                    continue;
                }
                $fields[$field] = true;
				$conditions = $orderItem->getSortConditions();
				foreach ($conditions as $condition)
					$sortConditions[] = $condition;
            }

            if(count($sortConditions))
                $sortConditions[] = '_score';

            $this->query['body']['sort'] = $sortConditions;
        }
    }

    protected function initBaseFilter($partnerId, array $statuses, $objectId)
    {
        $partnerStatus = array();
        foreach ($statuses as $status)
        {
            $partnerStatus[] = elasticSearchUtils::formatPartnerStatus($partnerId, $status);
        }

		$partnerStatusQuery = new kESearchTermsQuery('partner_status', $partnerStatus);
		$this->mainBoolQuery->addToFilter($partnerStatusQuery);
		
        if($objectId)
        {
			$id = elasticSearchUtils::formatSearchTerm($objectId);
			$idQuery = new kESearchTermQuery('_id', $id);
			$this->mainBoolQuery->addToFilter($idQuery);
        }

        //return only the object id
        $this->query['body']['_source'] = false;
    }

    protected function addGlobalHighlights()
	{
        $this->queryAttributes->getQueryHighlightsAttributes()->setScopeToGlobal();
        $numOfFragments = elasticSearchUtils::getNumOfFragmentsByConfigKey(self::GLOBAL_HIGHLIGHT_CONFIG);
        $highlight = new kESearchHighlightQuery($this->queryAttributes->getQueryHighlightsAttributes()->getFieldsToHighlight(), $numOfFragments);
        $highlight = $highlight->getFinalQuery();
        if($highlight)
            $this->query['body']['highlight'] = $highlight;
	}

    protected function applyElasticSearchConditions()
    {
        $this->query['body']['query'] = $this->mainBoolQuery->getFinalQuery();
    }

    protected function initQueryAttributes($partnerId, $objectId)
    {
        $this->initPartnerLanguages($partnerId);
        $this->queryAttributes->setObjectId($objectId);
        $this->initOverrideInnerHits($objectId);
    }

    protected function initPartnerLanguages($partnerId)
    {
        $partner = PartnerPeer::retrieveByPK($partnerId);
        if(!$partner)
            return;

        $partnerLanguages = $partner->getESearchLanguages();
        if(!count($partnerLanguages))
        {
            //if no languages are set for partner - set the default to english
            $partnerLanguages = array('english');
        }

        $this->queryAttributes->setPartnerLanguages($partnerLanguages);
    }

    protected function initOverrideInnerHits($objectId)
    {
        if(!$objectId)
            return;

        $innerHitsConfig = kConf::get('innerHits', 'elastic');
        $overrideInnerHitsSize = isset($innerHitsConfig['innerHitsWithObjectId']) ? $innerHitsConfig['innerHitsWithObjectId'] : null;
        $this->queryAttributes->setOverrideInnerHitsSize($overrideInnerHitsSize);
    }

    protected function addSearchTermsToSearchHistory($resultCount)
    {
        if (!$resultCount)
        {
            KalturaLog::log("Not adding search terms to search history result count[$resultCount]");
            return;
        }

        $searchTerms = $this->queryAttributes->getSearchHistoryTerms();
        $searchTerms = array_unique($searchTerms);
        $searchTerms = array_values($searchTerms);
        if (!$searchTerms)
        {
            KalturaLog::log("Empty search terms, not adding to search history");
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

}

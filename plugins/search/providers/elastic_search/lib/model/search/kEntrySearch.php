<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kEntrySearch extends kBaseESearch
{

    protected $isInitialized;
    private $entryEntitlementQuery;
    private $parentEntryEntitlementQuery;

    public function __construct()
    {
        $this->isInitialized = false;
		$this->boostItemsBy = ESearchBoostField::CREATED_AT;
        parent::__construct();
		$this->queryAttributes->setQueryFilterAttributes(new ESearchEntryQueryFilterAttributes());
    }

    protected function handleDisplayInSearch()
    {
        if($this->queryAttributes->getObjectId())
            return;

        $displayInSearchQuery = $this->queryAttributes->getQueryFilterAttributes()->getDisplayInSearchFilter();
        if ($displayInSearchQuery)
        {
            $this->mainBoolQuery->addToFilter($displayInSearchQuery);
        }
    }

    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $entriesStatus = array(), $objectIdsCsvStr = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations = null, $objectIdsNotIn = null)
    {
        kEntryElasticEntitlement::init();
        if (!count($entriesStatus))
            $entriesStatus = array(entryStatus::READY);
        $this->initQuery($entriesStatus, $objectIdsCsvStr, $pager, $order, $aggregations, $objectIdsNotIn);
        $this->initEntitlement($eSearchOperator, $objectIdsCsvStr, $objectIdsNotIn);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

	protected function boostItems()
	{
		$query = $this->query['body']['query'];
		unset($this->query['body']['query']);
		$this->query['body']['query']['function_score']['query'] = $query;
		
		switch ($this->boostItemsBy)
		{
			case ESearchBoostField::CREATED_AT:
			default:
			{
				$this->query['body']['query']['function_score']['functions'][] = array(ESearchBoostFunction::EXP => array(ESearchBoostField::CREATED_AT => array (
					'origin' => ESearchCreatedAtBoostItems::ORIGIN_NOW,
					'scale' => ESearchCreatedAtBoostItems::SCALE_30D,
					'decay' => ESearchCreatedAtBoostItems::DECAY_HALF)));
				$this->query['body']['query']['function_score']['boost_mode'] = ESearchBoostMode::MULTIPLY;
			}
		}

	}

    protected function initQuery(array $statuses, $objectIdsCsvStr, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations = null, $objectIdsNotIn = null)
    {
        $indexName = kBaseESearch::getElasticIndexNamePerPartner(ElasticIndexMap::ELASTIC_ENTRY_INDEX, kCurrentContext::getCurrentPartnerId());
        $this->query = array(
            'index' => $indexName,
            'type' => ElasticIndexMap::ELASTIC_ENTRY_TYPE
        );

        KalturaLog::debug("Index -" . $indexName);

        parent::initQuery($statuses, $objectIdsCsvStr, $pager, $order, $aggregations, $objectIdsNotIn);
    }

    protected function initEntitlement(ESearchOperator $eSearchOperator, $objectIdsCsvStr, $objectIdsNotIn = null)
    {
        kEntryElasticEntitlement::setFilteredCategoryIds($eSearchOperator, $objectIdsCsvStr, $objectIdsNotIn);
        $contributors = kEntryElasticEntitlement::getEntitlementContributors();
        foreach ($contributors as $contributor)
        {
            if($contributor::shouldContribute())
            {
                if(!$this->isInitialized)
                    $this->initEntryEntitlementQueries();
                $contributor::applyCondition($this->entryEntitlementQuery, $this->parentEntryEntitlementQuery);
            }
        }
    }

    protected function initEntryEntitlementQueries()
    {
        $this->parentEntryEntitlementQuery = null;

        if(kEntryElasticEntitlement::$parentEntitlement)
        {
            $EntitlementQueryBool = new kESearchBoolQuery();

            //Validate that parent entry property exist
            $parentQueryBool = new kESearchBoolQuery();
            $parentExistQuery = new kESearchExistsQuery('parent_id');
            $parentQueryBool->addToFilter($parentExistQuery);
            //assign by reference to create alias
            $this->parentEntryEntitlementQuery = &$parentQueryBool;
            $EntitlementQueryBool->addToShould($parentQueryBool);

            //Validate that parent entry property does not exist
            $entryQueryBool = new kESearchBoolQuery();
            $parentNotExistQuery = new kESearchExistsQuery('parent_id');
            $entryQueryBool->addToMustNot($parentNotExistQuery);
            //assign by reference to create alias
            $this->entryEntitlementQuery = &$entryQueryBool;
            $EntitlementQueryBool->addToShould($entryQueryBool);

            //add to main query filter
            $this->mainBoolQuery->addToFilter($EntitlementQueryBool);
        }
        else
        {
            $EntitlementQueryBool = new kESearchBoolQuery();
            //assign by reference to create alias
            $this->entryEntitlementQuery = &$EntitlementQueryBool;
            //add to main query filter
            $this->mainBoolQuery->addToFilter($EntitlementQueryBool);
        }
        $this->isInitialized = true;
    }

    public function getElasticTypeName()
    {
        return ElasticIndexMap::ELASTIC_ENTRY_TYPE;
    }

    public function fetchCoreObjectsByIds($ids)
    {
        $entries = entryPeer::retrieveByPKsNoFilter($ids);
        entryPeer::fetchPlaysViewsData($entries);
        return $entries;
    }

}

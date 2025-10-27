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

    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $entriesStatus = array(), $objectIdsCsvStr = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations = null, $objectIdsNotIn = null, ESearchScoreFunctionParams $scoreFunctionParams = null)
    {
        kEntryElasticEntitlement::init();
        if (!count($entriesStatus))
            $entriesStatus = array(entryStatus::READY);
        $this->initQuery($entriesStatus, $objectIdsCsvStr, $pager, $order, $aggregations, $objectIdsNotIn);
        $this->initEntitlement($eSearchOperator, $objectIdsCsvStr, $objectIdsNotIn);

		$result = $this->execSearch($eSearchOperator, $scoreFunctionParams);
        return $result;
    }

	protected function boostItems(ESearchScoreFunctionParams $scoreFunctionParams)
	{
		$query = $this->query['body']['query'];
		unset($this->query['body']['query']);
		$this->query['body']['query']['function_score']['query'] = $query;
		
		switch ($scoreFunctionParams->getScoreFunctionBoostField())
		{
			case ESearchScoreFunctionField::CREATED_AT:
			default:
			{
				$this->query['body']['query']['function_score']['functions'][] = array(
					$scoreFunctionParams->getScoreFunctionBoostType() => array( //exp
						$scoreFunctionParams->getScoreFunctionBoostField() => array ( //CREATED_AT
						'origin' => $scoreFunctionParams->getOrigin(),//now
						'scale' => $scoreFunctionParams->getScale(),//30d
						'decay' => $scoreFunctionParams->getDecay())),//ESearchCreatedAtBoostItems::DECAY_HALF)));
					'weight' => $scoreFunctionParams->getWeight());//1
				$this->query['body']['query']['function_score']['boost_mode'] = $scoreFunctionParams->getScoreFunctionBoostMode();//ESearchScoreFunctionMode::MULTIPLY;
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

<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kEntrySearch extends kBaseSearch
{

    const PEER_NAME = 'entryPeer';

    protected $isInitialized;
    private $entryEntitlementQuery;
    private $parentEntryEntitlementQuery;
    protected static $entitlementContributors = array(
        'kElasticEntryDisableEntitlementCondition',
        'kElasticPublicEntriesEntitlementCondition',
        'kElasticUserCategoryEntryEntitlementCondition',
        'kElasticUserEntitlementCondition'
    );

    public function __construct()
    {
        $this->isInitialized = false;
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, $entriesStatus = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight= true)
    {
	    kEntryElasticEntitlement::init();
        if (!count($entriesStatus))
            $entriesStatus = array(entryStatus::READY);
        $this->initQuery($entriesStatus, $objectId, $pager, $order, $useHighlight);
        $this->initEntitlement();
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight = true)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_ENTRY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_ENTRY_TYPE
        );
        $statuses = $this->initEntryStatuses($statuses);
        $this->initDisplayInSearch($objectId);
        parent::initQuery($statuses, $objectId, $pager, $order, $useHighlight);

    }

    protected function initEntryStatuses($statuses)
    {
        $enumType = call_user_func(array('KalturaEntryStatus', 'getEnumClass'));

        $finalStatuses = array();
        foreach($statuses as $status)
            $finalStatuses[] = kPluginableEnumsManager::apiToCore($enumType, $status);

        return $finalStatuses;
    }

    protected function initEntitlement()
    {
        foreach (self::$entitlementContributors as $contributor)
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
            $entitlementQueryPath = &$this->query['body']['query']['bool']['filter'][];
            //Validate that parent entry property exist
            $entitlementQueryPath['bool']['should'][0]['bool']['filter'][]['exists']['field'] = 'parent_id';

            //assign by reference to create name alias
            $this->parentEntryEntitlementQuery = &$entitlementQueryPath['bool']['should'][0]['bool'];

            //Validate that parent entry property does not exist
            $entitlementQueryPath['bool']['should'][1]['bool']['must_not'][]['exists']['field'] = 'parent_id';
            //assign by reference to create name alias
            $this->entryEntitlementQuery = &$entitlementQueryPath['bool']['should'][1]['bool'];
            $entitlementQueryPath['bool']['minimum_should_match'] = 1;
        }
        else
        {
            //entry query - assign by reference to create name alias
            $this->entryEntitlementQuery = &$this->query['body']['query']['bool']['filter'][]['bool'];
        }
        $this->isInitialized = true;
    }

    function getPeerName()
    {
        return self::PEER_NAME;
    }

    protected function initDisplayInSearch($objectId)
    {
        if($objectId)
            return;
    
        $displayInSearchQuery = new kESearchTermQuery('display_in_search', EntryDisplayInSearchType::SYSTEM);
        $displayInSearchBoolQuery = new kESearchBoolQuery();
        $displayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
        $this->mainBoolQuery->addToFilter($displayInSearchBoolQuery);
    }

}

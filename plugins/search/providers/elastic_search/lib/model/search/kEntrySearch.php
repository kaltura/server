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

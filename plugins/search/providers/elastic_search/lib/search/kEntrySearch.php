<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
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
            //create parent entry part
            $this->query['body']['query']['bool']['filter'][1]['bool']['should'][0]['bool']['filter'] = array(
                array(
                    'exists' => array(
                        'field'=> 'parent_id'
                    )
                )
            );

            //assign by reference to create name alias
            $this->parentEntryEntitlementQuery = &$this->query['body']['query']['bool']['filter'][1]['bool']['should'][0]['bool'];

            //create entry part
            $this->query['body']['query']['bool']['filter'][1]['bool']['should'][1]['bool']['must_not'] = array(
                array(
                    'exists' => array(
                        'field'=> 'parent_id'
                    )
                )
            );
            //assign by reference to create name alias
            $this->entryEntitlementQuery = &$this->query['body']['query']['bool']['filter'][1]['bool']['should'][1]['bool'];
            $this->query['body']['query']['bool']['filter'][1]['bool']['minimum_should_match'] = 1;
        }
        else
        {
            //entry query - assign by reference to create name alias
            $this->entryEntitlementQuery = &$this->query['body']['query']['bool']['filter'][1]['bool'];
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

        //add display in search to filter
        $this->query['body']['query']['bool']['filter'][] = array(
            'bool' => array(
                'must_not' => array(
                    'term' => array('display_in_search' => EntryDisplayInSearchType::SYSTEM)
                )
            )
        );
    }

}

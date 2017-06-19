<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */
class kEntrySearch
{
    protected $elasticClient;
    protected $query;
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
        $this->elasticClient = new elasticClient();
        $this->isInitialized = false;
    }

    public function doSearch(ESearchOperator $eSearchOperator, $entriesStatus = array())
    {
	    kElasticEntitlement::init();
        if (!count($entriesStatus))
            $entriesStatus = array(entryStatus::READY);
        $this->initQuery($entriesStatus);
        $this->initEntitlement();
        $subQuery = kESearchQueryManager::createSearchQuery($eSearchOperator);
        $this->applyElasticSearchConditions($subQuery);
        KalturaLog::debug("@@NH [".print_r($this->query, true)."]");; //todo - remove after debug
        $result = $this->elasticClient->search($this->query);
        return $result;
    }
    
    protected function initQuery(array $entriesStatus)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_ENTRY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_ENTRY_TYPE
        );
        $partnerId = kElasticEntitlement::$partnerId;
        $this->initBasePartnerFilter($partnerId, $entriesStatus);
    }

    protected function initBasePartnerFilter($partnerId, array $entriesStatus)
    {
        $partnerStatus = array();
        foreach ($entriesStatus as $entryStatus)
        {
            $partnerStatus[] = "p{$partnerId}s{$entryStatus}";
        }

        $this->query['body'] = array(
            'query' => array(
                'bool' => array(
                    'filter' => array(
                        array(
                            'terms' => array('partner_status' => $partnerStatus)
                        )
                    )
                )
            )
        );
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

        if(kElasticEntitlement::$parentEntitlement)
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

    protected function applyElasticSearchConditions($conditions)
    {
        if($conditions)
            $this->query['body']['query']['bool']['must'] = array($conditions);
    }

}

<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */
class kEntrySearch
{
    protected $elasticClient;
    protected $query;
    protected static $entitlementContributors = array(
        'kElasticEntryDisableEntitlementCondition',
        'kElasticPublicEntriesEntitlementCondition',
        'kElasticUserCategoryEntryEntitlementCondition',
        'kElasticUserEntitlementCondition'
    );

    public function __construct()
    {
        $this->elasticClient = new elasticClient();
    }

    /**
     * todo - before the call need to call kElasticEntitlement::init()
     */
    public function doSearch($elasticSearchConditions = null, $entriesStatus = array(entryStatus::READY))
    {
        $this->initQuery($entriesStatus);
        $this->initEntitlement();
        $this->applyElasticSearchConditions($elasticSearchConditions);
        echo print_r($this->query, true); //todo - remove after debug
        $result = $this->elasticClient->search($this->query);
        echo print_r($result, true); //todo - remove after debug
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
        $this->query['body'] = array(
            'query' => array(
                'bool' => array(
                    'filter' => array(
                        array(
                            'term' => array('partner_id' => $partnerId)
                        ),
                        array(
                            'terms' => array('status' => $entriesStatus)
                        )
                    )
                )
            )
        );
    }

    protected function initEntitlement()
    {
        $parentEntryQuery = null;
        
        //entry query - assign by reference to create name alias
        $entryQuery = &$this->query['body']['query']['bool'];

        if(kElasticEntitlement::$parentEntitlement)
        {
            //create parent entry part
            $this->query['body']['query']['bool']['should'][0]['bool']['filter'] = array(
                array(
                    'exists' => array(
                        'field'=> 'parent_id'
                    )
                )
            );

            //assign by reference to create name alias
            $parentEntryQuery = &$this->query['body']['query']['bool']['should'][0]['bool'];

            //create entry part
            $this->query['body']['query']['bool']['should'][1]['bool']['must_not'] = array(
                array(
                    'exists' => array(
                        'field'=> 'parent_id'
                    )
                )
            );
            //assign by reference to create name alias
            $entryQuery = &$this->query['body']['query']['bool']['should'][1]['bool'];
            $this->query['body']['query']['bool']['minimum_should_match'] = 1;
        }

        foreach (self::$entitlementContributors as $contributor)
        {
            if($contributor::shouldContribute())
            {
                $contributor::applyCondition($entryQuery, $parentEntryQuery);
            }
        }

    }

    protected function applyElasticSearchConditions($conditions)
    {
        if($conditions)
            $this->query['body']['query']['bool']['must'] = array($conditions);
    }

}

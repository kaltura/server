<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */

class kUserSearch extends kBaseSearch
{
    const PEER_NAME = 'kuserPeer';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), kPager $pager = null, ESearchOrderBy $order = null)
    {
        kUserElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(KuserStatus::ACTIVE);
        $this->initQuery($statuses, $pager, $order);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
            'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE
        );

        parent::initQuery($statuses, $pager, $order);
    }

    function getPeerName()
    {
        return self::PEER_NAME;
    }
}

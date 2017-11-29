<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

class kUserSearch extends kBaseSearch
{
    const PEER_NAME = 'kuserPeer';
    const PEER_RETRIEVE_FUNCTION_NAME = 'retrieveByPKs';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight = true)
    {
        kUserElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(KuserStatus::ACTIVE);
        $this->initQuery($statuses, $objectId, $pager, $order, $useHighlight);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight = true)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
            'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE
        );

        parent::initQuery($statuses, $objectId, $pager, $order, $useHighlight);
    }

    function getPeerName()
    {
        return self::PEER_NAME;
    }

    public function getPeerRetrieveFunctionName()
    {
        return self::PEER_RETRIEVE_FUNCTION_NAME;
    }

}

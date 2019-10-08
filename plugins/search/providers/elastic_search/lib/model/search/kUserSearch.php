<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

class kUserSearch extends kBaseESearch
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectId = null, ESearchOrderBy $order = null)
    {
        kUserElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(KuserStatus::ACTIVE);
        $this->initQuery($statuses, $objectId, $pager, $order);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
            'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE
        );

        parent::initQuery($statuses, $objectId, $pager, $order);
    }

    public function getElasticTypeName()
    {
        return ElasticIndexMap::ELASTIC_KUSER_TYPE;
    }

    public function fetchCoreObjectsByIds($ids)
    {
        return kuserPeer::retrieveByPKs($ids);
    }

}

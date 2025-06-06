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
    
    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectIdsCsvStr = null, ESearchOrderBy $order = null)
    {
        kUserElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(KuserStatus::ACTIVE);
        $this->initQuery($statuses, $objectIdsCsvStr, $pager, $order);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectIdsCsvStr, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null, $objectIdsNotIn = null)
    {
        $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_KUSER_INDEX, kCurrentContext::getCurrentPartnerId());
        $this->query = array(
            'index' => $indexName,
            'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE
        );

        KalturaLog::debug("Index -" . $indexName);

        parent::initQuery($statuses, $objectIdsCsvStr, $pager, $order);
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

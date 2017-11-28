<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

class kCategorySearch extends kBaseSearch
{
    const PEER_NAME = 'categoryPeer';

    public function __construct()
    {
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight = true)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);
        $this->initQuery($statuses, $objectId, $pager, $order, $useHighlight);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight = true)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        parent::initQuery($statuses, $objectId, $pager, $order, $useHighlight);
    }

    function getPeerName()
    {
        return self::PEER_NAME;
    }

}

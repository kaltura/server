<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */

class kCategorySearch extends kBaseSearch
{
    const PEER_NAME = 'categoryPeer';

    public function __construct()
    {
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), kPager $pager = null, ESearchOrderBy $order = null, $useHighlight)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);
        $this->initQuery($statuses, $pager, $order, $useHighlight);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        parent::initQuery($statuses, $pager, $order, $useHighlight);
    }

    function getPeerName()
    {
        return self::PEER_NAME;
    }

}

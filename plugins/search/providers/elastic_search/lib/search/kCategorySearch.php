<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */

class kCategorySearch extends kBaseSearch
{

    public function __construct()
    {
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), kPager $pager = null, ESearchOrderBy $order = null)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);
        $this->initQuery($statuses, $pager, $order);
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        parent::initQuery($statuses, $pager, $order);
    }
}
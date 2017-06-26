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

    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), kPager $pager = null)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);
        $this->initQuery($statuses);
        $subQuery = kESearchQueryManager::createSearchQuery($eSearchOperator);
        $this->applyElasticSearchConditions($subQuery);
        KalturaLog::debug("@@NH [".print_r($this->query, true)."]");; //todo - remove after debug
        $result = $this->elasticClient->search($this->query);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        parent::initQuery($statuses, $pager);
    }
}
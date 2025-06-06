<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

class kCategorySearch extends kBaseESearch
{

    public function __construct()
    {
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectIdsCsvStr = null, ESearchOrderBy $order = null)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);

        $this->initQuery($statuses, $objectIdsCsvStr, $pager, $order);
        $this->initEntitlement();
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectIdsCsvStr, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null, $objectIdsNotIn = null)
    {
        $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_CATEGORY_INDEX, kCurrentContext::getCurrentPartnerId());
        $this->query = array(
            'index' =>  $indexName,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        KalturaLog::debug("Index -" . $indexName);

        parent::initQuery($statuses, $objectIdsCsvStr, $pager, $order);
    }

    private function initEntitlement()
	{
		$entitlementFilterQueries = kCategoryElasticEntitlement::getEntitlementFilterQueries();
		if($entitlementFilterQueries)
		{
			$this->mainBoolQuery->addQueriesToFilter($entitlementFilterQueries);
		}
	}

    public function getElasticTypeName()
    {
        return ElasticIndexMap::ELASTIC_CATEGORY_TYPE;
    }

    public function fetchCoreObjectsByIds($ids)
    {
        return categoryPeer::retrieveByPKsNoFilter($ids);
    }

}

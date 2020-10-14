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

    public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectId = null, ESearchOrderBy $order = null)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);

        $this->initQuery($statuses, $objectId, $pager, $order);
        $this->initEntitlement();
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null)
    {
        $this->query = array(
            'index' => kBaseESearch::getElasticIndexNamePerPartner(ElasticIndexMap::ELASTIC_CATEGORY_INDEX, kCurrentContext::getCurrentPartnerId()),
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
        );

        parent::initQuery($statuses, $objectId, $pager, $order);
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

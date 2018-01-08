<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

class kCategorySearch extends kBaseSearch
{
    const PEER_NAME = 'categoryPeer';
    const PEER_RETRIEVE_FUNCTION_NAME = 'retrieveByPKs';

    public function __construct()
    {
        parent::__construct();
    }

    public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null)
    {
        kCategoryElasticEntitlement::init();
        if (!count($statuses))
            $statuses = array(CategoryStatus::ACTIVE);

        $this->initQuery($statuses, $objectId, $pager, $order);
        $this->initEntitlement();
        $result = $this->execSearch($eSearchOperator);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $this->query = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
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

    function getPeerName()
    {
        return self::PEER_NAME;
    }

    public function getPeerRetrieveFunctionName()
    {
        return self::PEER_RETRIEVE_FUNCTION_NAME;
    }

}

<?php
/**
 * @package plugins.beacon
 * @subpackage model.search
 */
class kScheduledResourceSearch extends kBaseSearch
{
	public function __construct()
	{
		parent::__construct();
		$beaconElasticConfig = kConf::get('beacon', 'elastic');
		if(!$beaconElasticConfig)
		{
			throw new KalturaAPIException("Missing beacon configuration");
		}

		$host = isset($beaconElasticConfig['elasticHost']) ? $beaconElasticConfig['elasticHost'] : null;
		$port = isset($beaconElasticConfig['elasticPort']) ? $beaconElasticConfig['elasticPort'] : null;
		$this->elasticClient = new elasticClient($host, $port);
	}

	public function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectId = null,
							 ESearchOrderBy $order = null)
	{
		kScheduledResourceSearchEntitlement::init();
		$this->initQuery($statuses, $objectId, $pager, $order);
		$this->initEntitlement();
		$result = $this->execSearch($eSearchOperator);
		return $result;
	}

	protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null,  ESearchAggregations $aggregations=null)
	{
		$this->query = array(elasticClient::ELASTIC_INDEX_KEY => BeaconIndexName::SCHEDULED_RESOURCE_INDEX);
		$partnerId = kBaseElasticEntitlement::$partnerId;
		$this->initQueryAttributes($partnerId, $objectId);
		$this->initPager($pager);
		$this->initOrderBy($order);
	}

	protected function initOrderBy(ESearchOrderBy $order = null)
	{
		if($order)
		{
			$sortConditions = $this->getSortConditions($order);
			$this->query['body']['sort'] = $sortConditions;
		}
	}

	protected function execSearch(ESearchOperator $eSearchOperator)
	{
		$subQuery = $eSearchOperator::createSearchQuery($eSearchOperator->getSearchItems(), null, $this->queryAttributes, $eSearchOperator->getOperator());
		$this->mainBoolQuery->addToFilter($subQuery);
		$this->applyElasticSearchConditions();
		$result = $this->elasticClient->search($this->query, true, true);
		return $result;
	}

	protected function initEntitlement()
	{
		$entitlementFilterQueries = kScheduledResourceSearchEntitlement::getEntitlementFilterQueries();
		if($entitlementFilterQueries)
		{
			$this->mainBoolQuery->addQueriesToFilter($entitlementFilterQueries);
		}
	}
}

<?php
/**
 * @package plugins.beacon
 * @subpackage model.search
 */
class kScheduledResourceSearch extends kBaseSearch
{
	const INDEX_NAME = 'beacon_scheduled_resource_index_search';
	public function __construct()
	{
		parent::__construct();
		$beaconElasticConfig = kConf::get('beacon', 'elastic');
		$host = isset($beaconElasticConfig['elasticHost']) ? $beaconElasticConfig['elasticHost'] : null;
		$port = isset($beaconElasticConfig['elasticPort']) ? $beaconElasticConfig['elasticPort'] : null;
		$this->elasticClient = new elasticClient($host, $port);
	}

	public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null,
							 ESearchOrderBy $order = null)
	{
		elasticSearchUtils::$shouldLower = false;
		kScheduledResourceSearchEntitlement::init();
		$this->initQuery($statuses, $objectId, $pager, $order);
		$this->initEntitlement();
		$result = $this->execSearch($eSearchOperator);
		elasticSearchUtils::$shouldLower = true;
		return $result;
	}

	protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null)
	{
		$this->query = array('index' => self::INDEX_NAME);
		$partnerId = kBaseElasticEntitlement::$partnerId;
		$this->initQueryAttributes($partnerId, $objectId);
		$this->initPager($pager);
		$this->initOrderBy($order);
		$searchQuery[elasticClient::ELASTIC_INDEX_KEY] = kBeacon::ELASTIC_BEACONS_INDEX_NAME;
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

	public function getPeerName()
	{
		// TODO: Implement getPeerName() method.
	}

	public function getPeerRetrieveFunctionName()
	{
		// TODO: Implement getPeerRetrieveFunctionName() method.
	}

	public function getElasticTypeName()
	{
		// TODO: Implement getElasticTypeName() method.
	}

	private function initEntitlement()
	{
		$entitlementFilterQueries = kScheduledResourceSearchEntitlement::getEntitlementFilterQueries();
		if($entitlementFilterQueries)
		{
			$this->mainBoolQuery->addQueriesToFilter($entitlementFilterQueries);
		}
	}
}

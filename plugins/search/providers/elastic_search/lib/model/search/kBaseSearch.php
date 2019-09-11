<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */

abstract class kBaseSearch
{
	protected $elasticClient;
	protected $query;
	protected $queryAttributes;
	protected $mainBoolQuery;

	protected $filterOnlyContext;
	protected $forceInnerHitsSizeOverride;

	public function __construct()
	{
		$this->elasticClient = new elasticClient();
		$this->queryAttributes = new ESearchQueryAttributes();
		$this->mainBoolQuery = new kESearchBoolQuery();
		$this->filterOnlyContext = false;
		$this->forceInnerHitsSizeOverride = false;
	}

	public abstract function doSearch(ESearchOperator $eSearchOperator, kPager $pager = null, $statuses = array(), $objectId = null, ESearchOrderBy $order = null);

	/**
	 * @return ESearchQueryAttributes
	 */
	public function getQueryAttributes()
	{
		return $this->queryAttributes;
	}

	protected function handleDisplayInSearch()
	{
	}

	protected abstract function execSearch(ESearchOperator $eSearchOperator);

	protected abstract function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null, ESearchAggregations $aggregations=null);

	protected function initPager(kPager $pager = null)
	{
		if ($pager)
		{
			$this->query['from'] = $pager->calcOffset();
			$this->query['size'] = $pager->getPageSize();
		}
	}

	protected function initOrderBy(ESearchOrderBy $order = null)
	{
		if($order)
		{
			$sortConditions = $this->getSortConditions($order);
			if(count($sortConditions))
			{
				$sortConditions[] = '_score';
			}

			$this->query['body']['sort'] = $sortConditions;
		}
	}

	protected function initAggregations(ESearchAggregations $aggregations = null)
	{
		if(!$aggregations)
		{
			return;
		}

		$aggs = array();

		foreach ($aggregations->getAggregations() as $aggregation)
		{
			/* var $aggregation ESearchAggregationItem */
			$aggregationKey = $aggregation->getAggregationKey() . ':' . $aggregation->getFieldName();
			$aggs[$aggregationKey] = $aggregation->getAggregationCommand();
		}
		if($aggs)
		{
			$this->query['body']['aggs'] = $aggs;
		}
	}

	protected function getSortConditions(ESearchOrderBy $order)
	{
		$orderItems = $order->getOrderItems();
		$fields = array();
		$sortConditions = array();
		foreach ($orderItems as $orderItem)
		{
			$field = $orderItem->getSortField();
			if(isset($fields[$field]))
			{
				KalturaLog::log("Order by condition already set for field [$field]" );
				continue;
			}

			$fields[$field] = true;
			$conditions = $orderItem->getSortConditions();
			foreach ($conditions as $condition)
			{
				$sortConditions[] = $condition;
			}
		}

		return $sortConditions;
	}

	protected function initBaseFilter($partnerId, array $statuses, $objectId)
	{
		$partnerStatus = array();
		foreach ($statuses as $status)
		{
			$partnerStatus[] = elasticSearchUtils::formatPartnerStatus($partnerId, $status);
		}

		$partnerStatusQuery = new kESearchTermsQuery('partner_status', $partnerStatus);
		$this->mainBoolQuery->addToFilter($partnerStatusQuery);

		if($objectId)
		{
			$id = elasticSearchUtils::formatSearchTerm($objectId);
			$idQuery = new kESearchTermQuery('_id', $id);
			$this->mainBoolQuery->addToFilter($idQuery);
		}

		//return only the object id
		$this->query['body']['_source'] = false;
	}

	protected function applyElasticSearchConditions()
	{
		$this->query['body']['query'] = $this->mainBoolQuery->getFinalQuery();
	}

	protected function initQueryAttributes($partnerId, $objectId)
	{
		$this->initPartnerLanguagesSynonyms($partnerId);
		$this->queryAttributes->setObjectId($objectId);
		$this->initOverrideInnerHits($objectId);
	}

	protected function initPartnerLanguagesSynonyms($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
		{
			return;
		}

		$partnerLanguages = $partner->getESearchLanguages();
		if(!count($partnerLanguages))
		{
			//if no languages are set for partner - set the default to english
			$partnerLanguages = array('english');
		}

		$this->queryAttributes->setPartnerLanguages($partnerLanguages);
		$this->queryAttributes->setIgnoreSynonymsOnPartner($partner->getIgnoreSynonymEsearch());
	}

	protected function initOverrideInnerHits($objectId)
	{
		if(!$objectId && !$this->forceInnerHitsSizeOverride)
		{
			return;
		}

		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$overrideInnerHitsSize = isset($innerHitsConfig['innerHitsWithObjectId']) ? $innerHitsConfig['innerHitsWithObjectId'] : null;
		$this->queryAttributes->setOverrideInnerHitsSize($overrideInnerHitsSize);
	}

	public function setFilterOnlyContext()
	{
		$this->filterOnlyContext = true;
	}

	public function setForceInnerHitsSizeOverride()
	{
		$this->forceInnerHitsSizeOverride = true;
	}

}
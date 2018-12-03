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

    public function __construct()
    {
        $this->elasticClient = new elasticClient();
        $this->queryAttributes = new ESearchQueryAttributes();
		$this->mainBoolQuery = new kESearchBoolQuery();
    }

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null);

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

    protected abstract function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null);

    protected function initPager(kPager $pager = null)
    {
        if($pager)
        {
            $this->query['from'] = $pager->calcOffset();
            $this->query['size'] = $pager->calcPageSize();
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
        $this->initPartnerLanguages($partnerId);
        $this->queryAttributes->setObjectId($objectId);
        $this->initOverrideInnerHits($objectId);
    }

    protected function initPartnerLanguages($partnerId)
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
    }

    protected function initOverrideInnerHits($objectId)
    {
        if(!$objectId)
        {
			return;
		}

        $innerHitsConfig = kConf::get('innerHits', 'elastic');
        $overrideInnerHitsSize = isset($innerHitsConfig['innerHitsWithObjectId']) ? $innerHitsConfig['innerHitsWithObjectId'] : null;
        $this->queryAttributes->setOverrideInnerHitsSize($overrideInnerHitsSize);
    }
}

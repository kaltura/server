<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */

abstract class kBaseSearch
{
    protected $elasticClient;
    protected $query;
    protected $queryAttributes;

    public function __construct()
    {
        $this->elasticClient = new elasticClient();
        $this->queryAttributes = new ESearchQueryAttributes();
    }

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null, ESearchOrderBy $order = null);

    public abstract function getPeerName();

    protected function execSearch(ESearchOperator $eSearchOperator)
    {
        $subQuery = $eSearchOperator->createSearchQuery($eSearchOperator->getSearchItems(), null, $this->queryAttributes, $eSearchOperator->getOperator());
        $this->applyElasticSearchConditions($subQuery);
        KalturaLog::debug("Elasticsearch query [".print_r($this->query, true)."]");
        $result = $this->elasticClient->search($this->query);
        return $result;
    }

    protected function initQuery(array $statuses, $objectId, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $partnerId = kBaseElasticEntitlement::$partnerId;
        $this->initQueryAttributes($partnerId, $objectId);
        $this->initBaseFilter($partnerId, $statuses, $objectId);
        $this->initPager($pager);
        $this->initOrderBy($order);
    }

    protected function initPager(kPager $pager = null)
    {
        if($pager)
        {
            $this->query['from'] = $pager->calcOffset();
            $this->query['size'] = $pager->getPageSize();
        }
    }

    protected function initOrderBy(ESearchOrderBy $order = null)
    {
        if($order)
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
                $sortConditions[] = array(
                    $field => array('order' => $orderItem->getSortOrder())
                );
            }

            if(count($sortConditions))
                $sortConditions[] = '_score';

            $this->query['body']['sort'] = $sortConditions;
        }
    }

    protected function initBaseFilter($partnerId, array $statuses, $objectId)
    {
        $partnerStatus = array();
        foreach ($statuses as $status)
        {
            $partnerStatus[] = elasticSearchUtils::formatPartnerStatus($partnerId, $status);
        }

        $this->query['body'] = array(
            'query' => array(
                'bool' => array(
                    'filter' => array(
                        array(
                            'terms' => array('partner_status' => $partnerStatus)
                        )
                    )
                )
            )
        );

        if($objectId)
        {
            $this->query['body']['query']['bool']['filter'][] = array(
                'term' => array('_id' => elasticSearchUtils::formatSearchTerm($objectId))
            );
        }

        //return only the object id
        $this->query['body']['_source'] = false;
    }

    protected function applyElasticSearchConditions($conditions)
    {
        $this->query['body']['query']['bool']['must'] = array($conditions);
    }

    protected function initQueryAttributes($partnerId, $objectId)
    {
        $this->initPartnerLanguages($partnerId);
        $this->initOverrideInnerHits($objectId);
    }

    protected function initPartnerLanguages($partnerId)
    {
        $partner = PartnerPeer::retrieveByPK($partnerId);
        if(!$partner)
            return;

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
            return;

        $innerHitsConfig = kConf::get('innerHits', 'elastic');
        $overrideInnerHitsSize = isset($innerHitsConfig['innerHitsWithObjectId']) ? $innerHitsConfig['innerHitsWithObjectId'] : null;
        $this->queryAttributes->setOverrideInnerHitsSize($overrideInnerHitsSize);
    }

}

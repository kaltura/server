<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.search
 */

abstract class kBaseSearch
{
    protected $elasticClient;
    protected $query;

    public function __construct()
    {
        $this->elasticClient = new elasticClient();
    }

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(),kPager $pager = null, ESearchOrderBy $order = null);

    protected function execSearch(ESearchOperator $eSearchOperator)
    {
        $subQuery = $eSearchOperator->createSearchQuery($eSearchOperator->getSearchItems(), null, $eSearchOperator->getOperator());
        $this->applyElasticSearchConditions($subQuery);
        KalturaLog::debug("Elasticsearch query [".print_r($this->query, true)."]");
        $result = $this->elasticClient->search($this->query);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null, ESearchOrderBy $order = null)
    {
        $partnerId = kBaseElasticEntitlement::$partnerId;
        $this->initBasePartnerFilter($partnerId, $statuses);
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

    protected function initBasePartnerFilter($partnerId, array $statuses)
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
    }

    protected function applyElasticSearchConditions($conditions)
    {
        $this->query['body']['query']['bool']['must'] = array($conditions);
    }

}

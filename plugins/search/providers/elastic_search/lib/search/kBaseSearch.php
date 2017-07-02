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

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(),kPager $pager =null);

    protected function execSearch(ESearchOperator $eSearchOperator)
    {
        $subQuery = kESearchQueryManager::createOperatorSearchQuery($eSearchOperator);
        KalturaLog::debug("@@WD [".print_r($subQuery, true)."]");
        $this->applyElasticSearchConditions($subQuery);
        KalturaLog::debug("@@NH [".print_r($this->query, true)."]");; //todo - remove after debug
        $result = $this->elasticClient->search($this->query);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null)
    {
        $partnerId = kBaseElasticEntitlement::$partnerId;
        $this->initBasePartnerFilter($partnerId, $statuses);
        $this->initPager($pager);
    }

    protected function initPager(kPager $pager = null)
    {
        if($pager)
        {
            $this->query['from'] = $pager->calcOffset();
            $this->query['size'] = $pager->getPageSize();
        }
    }

    protected function initBasePartnerFilter($partnerId, array $statuses)
    {
        $partnerStatus = array();
        foreach ($statuses as $status)
        {
            $partnerStatus[] = "p{$partnerId}s{$status}";
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
        if($conditions)
            $this->query['body']['query']['bool']['must'] = array($conditions);
    }
}

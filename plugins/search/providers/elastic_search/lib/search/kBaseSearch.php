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
}

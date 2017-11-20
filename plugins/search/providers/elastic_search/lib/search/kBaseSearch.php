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

    public abstract function doSearch(ESearchOperator $eSearchOperator, $statuses = array(),kPager $pager = null, ESearchOrderBy $order = null, $useHighlight);

    public abstract function getPeerName();

    protected function execSearch(ESearchOperator $eSearchOperator)
    {
        $subQuery = $eSearchOperator->createSearchQuery($eSearchOperator->getSearchItems(), null, $this->queryAttributes, $eSearchOperator->getOperator());
        $this->applyElasticSearchConditions($subQuery);
		$this->addGlobalHighlights();
        KalturaLog::debug("Elasticsearch query [".print_r($this->query, true)."]");
        $result = $this->elasticClient->search($this->query);
        return $result;
    }

    protected function initQuery(array $statuses, kPager $pager = null, ESearchOrderBy $order = null, $useHighlight)
    {
        $partnerId = kBaseElasticEntitlement::$partnerId;
        $this->initQueryAttributes($partnerId, $useHighlight);
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
        //return only the object id
        $this->query['body']['_source'] = false;
    }

    protected function addGlobalHighlights()
	{
		$highlight = self::addHighlightSection('global', $this->queryAttributes);
		if(isset($highlight))
		{
			$this->query['body']['highlight'] = $highlight;
		}
	}


	/**
	 * @param string $highlightScope
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array|null
	 */
	public static function addHighlightSection($highlightScope, $queryAttributes)
	{
		$highlight = null;
		$fieldsToHighlight = $queryAttributes->getFieldsToHighlight();
		if(!empty($fieldsToHighlight) && $queryAttributes->getUseHighlight())
		{
			$highlight = array();
			$highlight["type"] = "unified";
			$highlight["order"] = "score";
			$configurationName = $highlightScope."MaxNumberOfFragments";
			$innerHitsConfig = kConf::get('highlights', 'elastic');
			$innerHitsSize = isset($innerHitsConfig[$configurationName]) ? $innerHitsConfig[$configurationName] : 5;
			$highlight['number_of_fragments'] = $innerHitsSize;
			$highlight['fields'] = $fieldsToHighlight;
		}

		return $highlight;
	}

    protected function applyElasticSearchConditions($conditions)
    {
        $this->query['body']['query']['bool']['must'] = array($conditions);
    }

    protected function initQueryAttributes($partnerId, $useHighlight)
    {
        $this->initPartnerLanguages($partnerId);
        $this->queryAttributes->setUseHighlight($useHighlight);
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

}

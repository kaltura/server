<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticUserCategoryEntryEntitlementCondition extends kElasticBaseEntitlementCondition
{
    const MAX_CATEGORIES = 1024;

    protected static function getEntitlementCondition(array $params = array(), $fieldPrefix ='')
    {
        $conditions = array(
            array(
                'terms' => array(
                    "{$fieldPrefix}category_ids" => $params['category_ids']
                )
            )
        );
        return $conditions;
    }

    public static function applyCondition(&$entryQuery, &$parentEntryQuery)
    {
        $kuserId = kEntryElasticEntitlement::$kuserId;
        if(!$kuserId)
        {
            KalturaLog::log('cannot add userCategory to entry entitlement to elastic without a kuserId - setting kuser id to -1');
            $kuserId = -1;
        }
        //get category ids with $privacyContext
        $categories = self::getUserCategories($kuserId, kEntryElasticEntitlement::$privacyContext, kEntryElasticEntitlement::$privacy);
        if(count($categories) == 0)
            $categories = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);

        $params['category_ids'] = $categories;
        
        if($parentEntryQuery)
        {
            $conditions = self::getEntitlementCondition($params, 'parent_entry.');
            self::attachToQuery($parentEntryQuery, 'should', $conditions);
            $parentEntryQuery['minimum_should_match'] = 1;
        }
        $conditions = self::getEntitlementCondition($params);
        self::attachToQuery($entryQuery, 'should', $conditions);
        $entryQuery['minimum_should_match'] = 1;
    }

    public static function shouldContribute()
    {
        if(kEntryElasticEntitlement::$userCategoryToEntryEntitlement || kEntryElasticEntitlement::$entryInSomeCategoryNoPC)
            return true;
        
        return false;
    }

    protected static function getUserCategories($kuserId, $privacyContext = null, $privacy = null)
    {
        $params = array(
            'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
            'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE,
            'size' => self::MAX_CATEGORIES
        );

        $body = array(
            'query' => array(
                'bool' => array(
                    'filter' => array(
                        array(
                            'term' => array(
                                'partner_status' => 'p'.kEntryElasticEntitlement::$partnerId.'s'.CategoryStatus::ACTIVE
                            )
                        ),
                        array(
                            'bool' => array(
                                'should' => array(
                                    array(
                                        'terms' => array(
                                            'kuser_ids' => array(
                                                'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
                                                'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
                                                'id' => $kuserId,
                                                'path' => 'group_ids'
                                            )
                                        )
                                    ),
                                    array(
                                        'term' => array(
                                            'kuser_ids' => $kuserId
                                        )
                                    )
                                ),
                                'minimum_should_match' => 1
                            )
                        )
                    )
                )
            )
        );

        if(kEntryElasticEntitlement::$entryInSomeCategoryNoPC)
        {
            $body['query']['bool']['filter'][1]['bool']['should'][] = array(
                'bool' => array(
                    'must_not' => array(
                        'exists' => array(
                            'field' => 'privacy_context'
                        )
                    )
                )
            );
        }

        $privacyContexts = null;
        if (!$privacyContext || trim($privacyContext) == '')
            $privacyContexts = array(kEntitlementUtils::getDefaultContextString(kEntryElasticEntitlement::$partnerId));
        else
        {
            $privacyContexts = explode(',', $privacyContext);
            $privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, kEntryElasticEntitlement::$partnerId );
        }
        $body['query']['bool']['filter'][] = array('terms' => array('privacy_contexts' => $privacyContexts)); //todo add partner prefix , search in privacy contexts

        //todo add privacy on category
        if($privacy) //privacy is an array
        {
            $body['query']['bool']['filter'][] = array('terms' => array('privacy' => $privacy)); //todo add partner prefix
        }

        $params['body'] = $body;
        $elasticClient = new elasticClient();
        $results = $elasticClient->search($params);
        $categories = $results['hits']['hits'];
        $categoryIds = array();

        foreach ($categories as $category)
        {
            $categoryIds[] = $category['_id'];
        }
        return $categoryIds;
    }
}

<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticUserEntitlementCondition extends kElasticBaseEntitlementCondition
{
    protected static function getEntitlementCondition(array $params = array(), $fieldPrefix ='')
    {
        $conditions = array(
            array(
                'terms' => array(
                    "{$fieldPrefix}entitled_kusers_edit" => array(
                        'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
                        'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
                        'id' => $params['kuserId'],
                        'path' => 'group_ids'
                    )
                )
            ),
            array(
                'term' => array(
                    "{$fieldPrefix}entitled_kusers_edit" => $params['kuserId'],
                )
            ),
            array(
                'terms' => array(
                    "{$fieldPrefix}entitled_kusers_publish" => array(
                        'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
                        'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
                        'id' => $params['kuserId'],
                        'path' => 'group_ids'
                    )
                )
            ),
            array(
                'term' => array(
                    "{$fieldPrefix}entitled_kusers_publish" => $params['kuserId'],
                )
            ),
            array(
                'term' => array(
                    "{$fieldPrefix}kuser_id" => $params['kuserId']
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
            KalturaLog::log('cannot add user entitlement to elastic without a kuserId - setting kuser id to -1');
            $kuserId = -1;
        }
        $params['kuserId'] = $kuserId;
        
        if($parentEntryQuery)
        {
            //add parent conditions
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
        if(kEntryElasticEntitlement::$userEntitlement)
            return true;

        return false;
    }
}

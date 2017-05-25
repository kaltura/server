<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticPublicEntriesEntitlementCondition extends kElasticBaseEntitlementCondition
{
    protected static function getEntitlementCondition(array $params = array(), $fieldPrefix ='')
    {
        $conditions = array(
            array(
                'bool' => array(
                    'must_not' => array(
                        'exists' => array(
                            'field' => "{$fieldPrefix}category_ids"
                        )
                    )
                )
            )
        );
        return $conditions;
    }

    public static function applyCondition(&$entryQuery, &$parentEntryQuery)
    {
        if($parentEntryQuery)
        {
            $conditions = self::getEntitlementCondition(array(), 'parent_entry.');
            self::attachToQuery($parentEntryQuery, 'should', $conditions);
            $parentEntryQuery['minimum_should_match'] = 1;
        }
        $conditions = self::getEntitlementCondition();
        self::attachToQuery($entryQuery, 'should', $conditions);
        $entryQuery['minimum_should_match'] = 1;
    }

    public static function shouldContribute()
    {
       if(kElasticEntitlement::$publicEntries)
           return true;
        
        return false;
    }
}

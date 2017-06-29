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
        if(kEntryElasticEntitlement::$publicEntries)
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

        if(kEntryElasticEntitlement::$publicActiveEntries)
        {
            if($parentEntryQuery)
            {
                $conditions = self::getEntitlementCondition(array(), 'parent_entry.active_');
                self::attachToQuery($parentEntryQuery, 'should', $conditions);
                $parentEntryQuery['minimum_should_match'] = 1;
            }
            $conditions = self::getEntitlementCondition(array(), 'active_');
            self::attachToQuery($entryQuery, 'should', $conditions);
            $entryQuery['minimum_should_match'] = 1;
        }
    }

    public static function shouldContribute()
    {
       if(kEntryElasticEntitlement::$publicEntries || kEntryElasticEntitlement::$publicActiveEntries)
           return true;
        
        return false;
    }
}

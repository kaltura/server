<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticEntryDisableEntitlementCondition extends kElasticBaseEntitlementCondition
{
    protected static function getEntitlementCondition(array $params = array(), $fieldPrefix ='')
    {
        $conditions = array(
            array(
                'terms' => array(
                    "{$fieldPrefix}_id" => $params['entryIds']
                )
            )
        );
        return $conditions;
    }

    public static function applyCondition(&$entryQuery, &$parentEntryQuery)
    {
        $params['entryIds'] = kEntryElasticEntitlement::$entriesDisabledEntitlement;
        if($parentEntryQuery)
        {
            $conditions = self::getEntitlementCondition($params, 'parent_entry.entry');
            self::attachToQuery($parentEntryQuery, 'should', $conditions);
            $parentEntryQuery['minimum_should_match'] = 1;
        }
        $conditions = self::getEntitlementCondition($params);
        self::attachToQuery($entryQuery, 'should', $conditions);
        $entryQuery['minimum_should_match'] = 1;
    }

    public static function shouldContribute()
    {
        if(kEntryElasticEntitlement::$entriesDisabledEntitlement && count(kEntryElasticEntitlement::$entriesDisabledEntitlement))
            return true;
        
        return false;
    }
}

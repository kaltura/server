<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

abstract class kElasticBaseEntitlementCondition
{
    abstract public static function shouldContribute();
    abstract protected static function getEntitlementCondition(array $params = array(), $fieldPrefix ='');
    abstract public static function applyCondition(&$entryQuery, &$parentEntryQuery);

    protected static function attachToQuery(&$queryPath, $queryKey, array $queryValues)
    {
        if(isset($queryPath[$queryKey]))//append
        {
            foreach ($queryValues as $value)
            {
                $queryPath[$queryKey][] = $value;
            }
        }
        else
        {
            $queryPath[$queryKey] = $queryValues;
        }
    }
}

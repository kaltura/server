<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticEntryDisableEntitlementContributor implements IKalturaESearchEntryEntitlementContributor
{
	public static function shouldContribute()
	{
		if(kEntryElasticEntitlement::$entriesDisabledEntitlement && count(kEntryElasticEntitlement::$entriesDisabledEntitlement))
			return true;

		return false;
	}
	
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$conditions = new kESearchTermsQuery("{$fieldPrefix}_id", $params['entryIds']);
		return $conditions;
	}
	
	public static function applyCondition(&$entryQuery, &$parentEntryQuery)
	{
		$params['entryIds'] = kEntryElasticEntitlement::$entriesDisabledEntitlement;
		if($parentEntryQuery)
		{
			$conditions = self::getEntitlementCondition($params, 'parent_entry.entry');
			$parentEntryQuery->addToShould($conditions);
		}
		$conditions = self::getEntitlementCondition($params);
		$entryQuery->addToShould($conditions);
	}
}

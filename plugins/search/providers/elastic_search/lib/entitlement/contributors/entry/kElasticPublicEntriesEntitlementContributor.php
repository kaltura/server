<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticPublicEntriesEntitlementContributor implements IKalturaESearchEntryEntitlementContributor
{

	public static function shouldContribute()
	{
		if(kEntryElasticEntitlement::$publicEntries || kEntryElasticEntitlement::$publicActiveEntries)
			return true;
		
		return false;
	}

	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$condition = new kESearchBoolQuery();
		$existQuery = new kESearchExistsQuery("{$fieldPrefix}category_ids");
		$condition->addToMustNot($existQuery);
		return $condition;
	}

	public static function applyCondition(&$entryQuery, &$parentEntryQuery)
	{
		if(kEntryElasticEntitlement::$publicEntries)
		{
			if($parentEntryQuery)
			{
				$condition = self::getEntitlementCondition(array(), 'parent_entry.');
				$parentEntryQuery->addToShould($condition);
			}
			$condition = self::getEntitlementCondition();
			$entryQuery->addToShould($condition);
		}
		
		if(kEntryElasticEntitlement::$publicActiveEntries)
		{
			if($parentEntryQuery)
			{
				$condition = self::getEntitlementCondition(array(), 'parent_entry.active_');
				$parentEntryQuery->addToShould($condition);
			}
			$condition = self::getEntitlementCondition(array(), 'active_');
			$entryQuery->addToShould($condition);
		}
	}
}

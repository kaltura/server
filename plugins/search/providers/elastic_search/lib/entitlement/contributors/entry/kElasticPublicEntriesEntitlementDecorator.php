<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticPublicEntriesEntitlementDecorator implements IKalturaESearchEntryEntitlementDecorator
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
		$statuses = $params['category_statues'];
		$statuses = array_map('elasticSearchUtils::formatCategoryEntryStatus', $statuses);
		$termsQuery = new kESearchTermsQuery("{$fieldPrefix}category_ids", $statuses);
		$condition->addToMustNot($termsQuery);
		return $condition;
	}

	public static function applyCondition(&$entryQuery, &$parentEntryQuery)
	{

		if(kEntryElasticEntitlement::$publicEntries)
			$params['category_statues'] = array(CategoryEntryStatus::ACTIVE, CategoryEntryStatus::PENDING);
		else if(kEntryElasticEntitlement::$publicActiveEntries)
			$params['category_statues'] = array(CategoryEntryStatus::ACTIVE);

		if($parentEntryQuery)
		{
			$condition = self::getEntitlementCondition($params, 'parent_entry.');
			$parentEntryQuery->addToShould($condition);
		}
		$condition = self::getEntitlementCondition($params);
		$entryQuery->addToShould($condition);
	}
}

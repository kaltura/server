<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticDisplyAndMemberEntitlement extends kElasticCategoryEntitlementDecorator
{
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$query = new kESearchBoolQuery();
		$query->addToShould(self::getDisplayInSearchQuery());
		$membersQuery = self::getMembersQuery();
		if($membersQuery)
			$query->addToShould($membersQuery);

		return $query;
	}

	private static function getMembersQuery()
	{
		$kuser = null;
		$membersQuery = null;
		if (kCurrentContext::$ks)
			$kuser = kCurrentContext::getCurrentKsKuser();

		if ($kuser)
		{
			// get the groups that the user belongs to in case she is not associated to the category directly
			$kgroupIds = $kuser->getRelatedGroupIds();
			$kgroupIds = array_map('elasticSearchUtils::formatSearchTerm', $kgroupIds);
			//kuser ids are equivalent to members in our elastic search
			$membersQuery = new kESearchTermsQuery(ESearchCategoryFieldName::KUSER_IDS, $kgroupIds);
		}

		return $membersQuery;
	}

	private static function getDisplayInSearchQuery()
	{
		return new kESearchTermQuery(ESearchCategoryFieldName::DISPLAY_IN_SEARCH,DisplayInSearchType::PARTNER_ONLY);
	}
}

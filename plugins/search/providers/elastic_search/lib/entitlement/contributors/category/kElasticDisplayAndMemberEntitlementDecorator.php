<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticDisplayAndMemberEntitlementDecorator extends kElasticCategoryEntitlementDecorator
{
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$query = new kESearchBoolQuery();
		$query->addToShould(self::getDisplayInSearchQuery());
		return self::getMembersQuery($query);
	}


	/**
	 * @param kESearchBoolQuery $query
	 * @return kESearchTermsQuery
	 */
	private static function getMembersQuery($query)
	{
		$kuser = null;
		if (kCurrentContext::$ks)
			$kuser = kCurrentContext::getCurrentKsKuser();

		if ($kuser)
		{
            $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_KUSER_INDEX, kCurrentContext::getCurrentPartnerId());

            // get the groups that the user belongs to in case she is not associated to the category directly
			//kuser ids are equivalent to members in our elastic search
			$userGroupsQuery = ESearchGroupUserItem::createGroupIdsTermsQuery(ESearchCategoryFieldName::KUSER_IDS, $kuser->getId(), $indexName);
			$query->addToShould($userGroupsQuery);
			$userQuery = new kESearchTermQuery(ESearchCategoryFieldName::KUSER_IDS, $kuser->getId());
			$query->addToShould($userQuery);
		}

		return $query;
	}

	private static function getDisplayInSearchQuery()
	{
		return new kESearchTermQuery(ESearchCategoryFieldName::DISPLAY_IN_SEARCH,DisplayInSearchType::PARTNER_ONLY);
	}
}

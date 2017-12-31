<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticDisplayAndMemberEntitlement extends kElasticCategoryEntitlementDecorator
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
			// get the groups that the user belongs to in case she is not associated to the category directly
			//kuser ids are equivalent to members in our elastic search
			$userGroupsQuery = new kESearchTermsQuery(ESearchCategoryFieldName::KUSER_IDS,array(
				'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
				'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
				'id' => $kuser->getId(),
				'path' => ESearchUserFieldName::GROUP_IDS
			));

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

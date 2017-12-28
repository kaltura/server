<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kCategoryElasticEntitlement extends kBaseElasticEntitlement
{
	static function getEntitlementFilterQueries()
	{
		$result = null;
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$result = array(kCategoryElasticEntitlement::getPrivacyContextQuery());
			$query = new kESearchBoolQuery();
			$query->addToShould(kCategoryElasticEntitlement::getDisplayInSearchQuery());
			$membersQuery = kCategoryElasticEntitlement::getMembersQuery();
			if($membersQuery)
				$query->addToShould($membersQuery);

			$result[] = $query;
		}

		return $result;
	}

	private static function getMembersQuery()
	{
		$kuser = null;
		$membersQuery = null;
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
		if ($ksString <> '')
			$kuser = kCurrentContext::getCurrentKsKuser();

		if ($kuser)
		{
			// get the groups that the user belongs to in case she is not associated to the category directly
			$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuser->getId());
			$kgroupIds[] = $kuser->getId();
			$kgroupIds = array_map('elasticSearchUtils::formatSearchTerm', $kgroupIds);
			//kuser ids are equivalent to members in our elastic search
			$membersQuery = new kESearchTermsQuery(ESearchCategoryFieldName::KUSER_IDS, $kgroupIds);
		}

		return $membersQuery;
	}

	private static function getPrivacyContextQuery()
	{
		$privacyContexts = kEntitlementUtils::getKsPrivacyContext();
		if(!is_array($privacyContexts))
		{
			$privacyContexts = array($privacyContexts);
		}

		$privacyContexts = array_map('elasticSearchUtils::formatSearchTerm', $privacyContexts);
		 return new kESearchTermsQuery(ESearchCategoryFieldName::PRIVACY_CONTEXTS, $privacyContexts);
	}

	private static function getDisplayInSearchQuery()
	{
		return new kESearchTermQuery(ESearchCategoryFieldName::DISPLAY_IN_SEARCH,DisplayInSearchType::PARTNER_ONLY);
	}
}

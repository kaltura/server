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
		if (kEntitlementUtils::getEntitlementEnforcement()) {
			$result = array();
			//add context as filter
			$privacyContexts = kEntitlementUtils::getKsPrivacyContext();
			if(!is_array($privacyContexts))
			{
				$privacyContexts = array($privacyContexts);
			}

			$privacyContexts = array_map('elasticSearchUtils::formatSearchTerm', $privacyContexts);
			$privacyContextQuery = new kESearchTermsQuery(ESearchCategoryFieldName::PRIVACY_CONTEXTS, $privacyContexts);
			$result[] = $privacyContextQuery;
			$query = new kESearchBoolQuery();
			$displayInSearchQuery = new kESearchTermQuery(ESearchCategoryFieldName::DISPLAY_IN_SEARCH,DisplayInSearchType::PARTNER_ONLY);
			$query->addToShould($displayInSearchQuery);

			$kuser = null;
			$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
			if ($ksString <> '')
				$kuser = kCurrentContext::getCurrentKsKuser();

			if ($kuser)
			{
				// get the groups that the user belongs to in case she is not associated to the category directly
				$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuser->getId());
				$kgroupIds[] = $kuser->getId();
				$kgroupIds = array_map('elasticSearchUtils::formatSearchTerm', $kgroupIds);
				$membersQuery = new kESearchTermsQuery(ESearchCategoryFieldName::KUSER_IDS, $kgroupIds);
				$query->addToShould($membersQuery);
			}

			$result[] = $query;
		}

		return $result;
	}
}

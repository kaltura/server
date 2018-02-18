<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticPrivacyContextEntitlement extends kElasticCategoryEntitlementDecorator
{
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$privacyContexts = kEntitlementUtils::getKsPrivacyContext();
		if(!is_array($privacyContexts))
		{
			$privacyContexts = array($privacyContexts);
		}

		$privacyContexts = array_map('elasticSearchUtils::formatSearchTerm', $privacyContexts);
		return new kESearchTermsQuery(ESearchCategoryFieldName::PRIVACY_CONTEXTS, $privacyContexts);
	}
}

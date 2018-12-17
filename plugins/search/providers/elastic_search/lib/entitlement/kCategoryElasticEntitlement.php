<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kCategoryElasticEntitlement extends kBaseElasticEntitlement
{
	protected static $entitlementContributors = array(
		'kElasticDisplayAndMemberEntitlementDecorator',
		'kElasticPrivacyContextEntitlementDecorator',
	);
}

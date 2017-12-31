<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kCategoryElasticEntitlement extends kBaseElasticEntitlement
{
	protected static $entitlementContributors = array(
		'kElasticDisplayAndMemberEntitlement',
		'kElasticPrivacyContextEntitlement',
	);

	public static function getEntitlementFilterQueries()
	{
		$result = null;
		$contributors = self::getEntitlementContributors();
		foreach ($contributors as $contributor)
		{
			if($contributor::shouldContribute())
			{
				$result[] = $contributor::getEntitlementCondition();
			}
		}

		return $result;
	}
}

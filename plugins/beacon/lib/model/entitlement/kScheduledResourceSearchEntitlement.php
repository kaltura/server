<?php
/**
 * @package plugins.beacon
 * @subpackage model.entitlement
 */

class kScheduledResourceSearchEntitlement extends kBaseElasticEntitlement
{
	protected static $entitlementContributors = array(
		'kScheduledResourcePartnerEntitlementDecorator'
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

	protected static function initialize()
	{
		self::$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
	}
}

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

	protected static function initialize()
	{
		self::$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
	}
}

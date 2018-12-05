<?php
/**
 * @package plugins.beacon
 * @subpackage model.entitlement
 */
class kScheduledResourcePartnerEntitlementDecorator extends kScheduledResourceSearchEntitlementDecorator
{
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		return new kESearchTermQuery(BeaconScheduledResourceFieldName::PARTNER_ID, kBaseElasticEntitlement::$partnerId);
	}
}
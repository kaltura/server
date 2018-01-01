<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

abstract class kElasticCategoryEntitlementDecorator implements IKalturaESearchEntitlementDecorator
{
	public static function shouldContribute()
	{
		return kEntitlementUtils::getEntitlementEnforcement();
	}
}

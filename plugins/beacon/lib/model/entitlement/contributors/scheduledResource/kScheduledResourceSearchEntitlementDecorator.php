<?php
/**
 * @package plugins.beacon
 * @subpackage model.entitlement
 */
abstract class kScheduledResourceSearchEntitlementDecorator implements IKalturaESearchEntitlementDecorator
{
	public static function shouldContribute()
	{
		return true;
	}
}
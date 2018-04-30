<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

interface IKalturaESearchEntitlementDecorator
{
	public static function shouldContribute();
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix ='');
}

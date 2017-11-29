<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */
interface IKalturaESearchEntryEntitlementContributor
{
	public static function shouldContribute();
	public static function getEntitlementCondition(array $params = array(), $fieldPrefix ='');
	public static function applyCondition(&$entryQuery, &$parentEntryQuery);
}

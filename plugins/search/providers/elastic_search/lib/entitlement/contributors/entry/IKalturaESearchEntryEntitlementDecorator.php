<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

interface IKalturaESearchEntryEntitlementDecorator extends IKalturaESearchEntitlementDecorator
{
	public static function applyCondition(&$entryQuery, &$parentEntryQuery);
}

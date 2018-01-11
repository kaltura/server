<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

abstract class KalturaVendorProfileRuleOption extends KalturaObject
{
	abstract public function getVendorProfileRule($params = null);
}
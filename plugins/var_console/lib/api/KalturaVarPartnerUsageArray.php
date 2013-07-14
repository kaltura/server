<?php
/**
 * @package plugins.varConsole
 * @subpackage api.objects
 */
class KalturaVarPartnerUsageArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaVarPartnerUsageItem");
	}
}
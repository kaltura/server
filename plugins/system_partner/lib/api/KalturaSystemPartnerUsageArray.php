<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerUsageArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaSystemPartnerUsageItem");
	}
}
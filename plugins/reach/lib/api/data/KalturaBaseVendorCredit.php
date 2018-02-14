<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @abstract
 */

abstract class KalturaBaseVendorCredit extends KalturaObject implements IApiObjectFactory
{
	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$creditType = get_class($sourceObject);
		$credit = null;
		switch ($creditType)
		{
			case 'kVendorCredit':
				$credit = new KalturaVendorCredit();
				break;

			case 'kTimeRangeVendorCredit':
				$credit = new KalturaTimeRangeVendorCredit();
				break;

			case 'kReoccurringVendorCredit':
				$credit = new KalturaReoccurringVendorCredit();
				break;

			case 'kUnlimitedVendorCredit':
				$credit = new KalturaUnlimitedVendorCredit();
				break;
		}

		if ($credit)
			/* @var $object KalturaBaseVendorCredit */
			$credit->fromObject($sourceObject, $responseProfile);

		return $credit;
	}
}
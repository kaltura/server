<?php
/**
 * @package plugins.chargeBee
 * @subpackage api.objects
 */

class KalturaChargeBeeVendorIntegrationArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaChargeBeeVendorIntegrationArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaChargeBeeVendorIntegration();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaChargeBeeVendorIntegration' );
	}
}
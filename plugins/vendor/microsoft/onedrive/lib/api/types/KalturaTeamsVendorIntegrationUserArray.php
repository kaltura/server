<?php


class KalturaTeamsVendorIntegrationUserArray extends KalturaTypedArray
{

	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaTeamsVendorIntegrationUserArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaTeamsVendorIntegrationUser();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct( )
	{
		return parent::__construct ( "KalturaTeamsVendorIntegrationUser" );
	}

}
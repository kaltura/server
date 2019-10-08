<?php
/**
 * @package plugins.sso
 * @subpackage api.objects
 */
class KalturaSsoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaSsoArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSso();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		return $newArr;
	}

	public function __construct( )
	{
		return parent::__construct ('KalturaSso');
	}
}
<?php
/**
 * @package plugins.group
 * @subpackage api.objects
 */

class KalturaGroupArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaGroupArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaGroup();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct( )
	{
		return parent::__construct ( "KalturaGroup" );
	}
}
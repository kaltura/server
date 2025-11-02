<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserEntryArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserEntryArray();
		foreach($arr as $obj)
		{
			/* @var $obj UserEntry */
			$nObj = KalturaUserEntry::getInstance($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct( )
	{
		return parent::__construct ( "KalturaUserEntry" );
	}
}

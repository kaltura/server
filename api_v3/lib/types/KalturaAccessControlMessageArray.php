<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlMessageArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAccessControlMessageArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaAccessControlMessage();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaAccessControlMessage");
	}
}
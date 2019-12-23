<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptionsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaExportToCsvOptionsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaExportToCsvOptions();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
	public function __construct()
	{
		parent::__construct("KalturaExportToCsvOptions");
	}
}

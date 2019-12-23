<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptionsArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaExportToCsvOptionsArray();
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

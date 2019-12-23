<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptionsArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::log(print_r($arr, true));
		$newArr = new KalturaExportToCsvOptionsArray();
		foreach ($arr as $obj)
		{
			$nObj = new KalturaExportToCsvOptions();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		KalturaLog::log("az2:".print_r($arr, true));
		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaExportToCsvOptions");
	}
}

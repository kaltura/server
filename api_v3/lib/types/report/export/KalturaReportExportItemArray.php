<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportItemArray extends KalturaTypedArray
{

	public function __construct()
	{
		return parent::__construct("KalturaReportExportItem");
	}

	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaReportExportItemArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaReportExportItem();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}

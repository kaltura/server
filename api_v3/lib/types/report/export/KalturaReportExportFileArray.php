<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportFileArray extends KalturaTypedArray
{

	public function __construct()
	{
		return parent::__construct("KalturaReportExportFile");
	}

	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaReportExportFileArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaReportExportFile();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}

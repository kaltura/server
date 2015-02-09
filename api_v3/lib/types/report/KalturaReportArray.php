<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaReport");
	}
	
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaReportArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaReport();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
?>
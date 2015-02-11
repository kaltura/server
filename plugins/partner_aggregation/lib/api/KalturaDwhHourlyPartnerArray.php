<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.objects
 */
class KalturaDwhHourlyPartnerArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDwhHourlyPartnerArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaDwhHourlyPartner();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDwhHourlyPartner");	
	}
}
<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.objects
 */
class KalturaDwhHourlyPartnerArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaDwhHourlyPartnerArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaDwhHourlyPartner();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDwhHourlyPartner");	
	}
}
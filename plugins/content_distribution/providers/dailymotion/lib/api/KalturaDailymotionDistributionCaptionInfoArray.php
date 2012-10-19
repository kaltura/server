<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage api.objects
 */
class KalturaDailymotionDistributionCaptionInfoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr)
	{
		$newArr = new KalturaDailymotionDistributionCaptionInfoArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaDailymotionDistributionCaptionInfo();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDailymotionDistributionCaptionInfo");	
	}
}
<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage api.objects
 */
class KalturaCortexApiCaptionDistributionInfoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCortexApiCaptionDistributionInfoArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaCortexApiCaptionDistributionInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCortexApiCaptionDistributionInfo");	
	}
}

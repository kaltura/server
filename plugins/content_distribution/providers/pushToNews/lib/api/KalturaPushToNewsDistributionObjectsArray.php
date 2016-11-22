<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage api.objects
 */
class KalturaPushToNewsDistributionObjectsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaPushToNewsDistributionObjectsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaPushToNewsDistributionObject();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPushToNewsDistributionObject");
	}
}

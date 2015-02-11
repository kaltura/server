<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionThumbDimensionsArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDistributionThumbDimensionsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaDistributionThumbDimensions();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDistributionThumbDimensions");	
	}
}
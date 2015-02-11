<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage api.objects
 */
class KalturaFreewheelDistributionAssetPathArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaFreewheelDistributionAssetPathArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaFreewheelDistributionAssetPath();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFreewheelDistributionAssetPath");	
	}
}
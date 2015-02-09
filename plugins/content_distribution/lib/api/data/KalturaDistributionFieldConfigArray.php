<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionFieldConfigArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaDistributionFieldConfigArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaDistributionFieldConfig();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct('KalturaDistributionFieldConfig');	
	}
}

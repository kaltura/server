<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaGenericDistributionProviderArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaGenericDistributionProviderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaGenericDistributionProvider();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaGenericDistributionProvider");	
	}
}
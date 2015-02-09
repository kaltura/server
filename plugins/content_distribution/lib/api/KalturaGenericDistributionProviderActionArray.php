<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaGenericDistributionProviderActionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaGenericDistributionProviderActionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaGenericDistributionProviderAction();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaGenericDistributionProviderAction");	
	}
}
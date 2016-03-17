<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage api.objects
 */
class KalturaFacebookCaptionDistributionInfoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaFacebookCaptionDistributionInfoArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaFacebookCaptionDistributionInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFacebookCaptionDistributionInfo");
	}
}
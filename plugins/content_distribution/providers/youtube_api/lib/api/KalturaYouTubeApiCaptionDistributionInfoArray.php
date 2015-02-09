<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage api.objects
 */
class KalturaYouTubeApiCaptionDistributionInfoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaYouTubeApiCaptionDistributionInfoArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaYouTubeApiCaptionDistributionInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaYouTubeApiCaptionDistributionInfo");	
	}
}
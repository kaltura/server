<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionRemoteMediaFileArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDistributionRemoteMediaFileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaDistributionRemoteMediaFile();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDistributionRemoteMediaFile");	
	}
}
<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.objects
 */
class KalturaCaptionAssetItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCaptionAssetItemArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaCaptionAssetItem();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCaptionAssetItem");	
	}
}
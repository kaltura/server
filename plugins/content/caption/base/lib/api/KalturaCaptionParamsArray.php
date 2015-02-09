<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionParamsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCaptionParamsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = KalturaAssetParamsFactory::getAssetParamsInstance($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCaptionParams");	
	}
}
<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionPlaybackPluginDataArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCaptionPlaybackPluginDataArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaCaptionPlaybackPluginData();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaCaptionPlaybackPluginData");
	}
}
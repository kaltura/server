<?php
/**
 * @package plugins.transcript
 * @subpackage api.objects
 */
class KalturaTranscriptAssetArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaTranscriptAssetArray();
		if ($arr == null)
			return $newArr;
	
		foreach ($arr as $obj)
		{
			$nObj = KalturaAsset::getInstance($obj);
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaTranscriptAsset");
	}
}

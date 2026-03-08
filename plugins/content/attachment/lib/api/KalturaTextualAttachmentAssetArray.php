<?php
/**
 * @package plugins.attachment
 * @subpackage api.objects
 */
class KalturaTextualAttachmentAssetArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaTextualAttachmentAssetArray();
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
		parent::__construct("KalturaTextualAttachmentAsset");
	}
}

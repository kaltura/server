<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaClipDescriptionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaClipDescriptionArray();
		if ($arr == null)
			return $newArr;
		foreach ($arr as $obj)
		{
    		$nObj = new KalturaClipDescription();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaClipDescription");
	}
}

<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyCopyEnablerHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlayReadyCopyEnablerHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
    		$nObj = new KalturaPlayReadyCopyEnablerHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyCopyEnablerHolder");	
	}
}
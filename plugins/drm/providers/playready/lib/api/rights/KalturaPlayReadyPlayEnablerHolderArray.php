<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyPlayEnablerHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlayReadyPlayEnablerHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
    		$nObj = new KalturaPlayReadyPlayEnablerHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyPlayEnablerHolder");	
	}
}
<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyAnalogVideoOPIdHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlayReadyAnalogVideoOPIdHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
    		$nObj = new KalturaPlayReadyAnalogVideoOPIdHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyAnalogVideoOPIdHolder");	
	}
}
<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyContentKeyArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlayReadyContentKeyArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaPlayReadyContentKey();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyContentKey");	
	}
}
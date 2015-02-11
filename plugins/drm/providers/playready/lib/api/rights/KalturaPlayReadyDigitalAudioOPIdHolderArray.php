<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyDigitalAudioOPIdHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlayReadyDigitalAudioOPIdHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
    		$nObj = new KalturaPlayReadyDigitalAudioOPIdHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyDigitalAudioOPIdHolder");	
	}
}
<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyRightArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaPlayReadyRightArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			if($nObj)
			{
				$nObj->fromObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPlayReadyRight");	
	}
	
	private static function getInstanceByDbObject($obj)
	{
		if($obj instanceof PlayReadyCopyRight)
			return new KalturaPlayReadyCopyRight();
		if($obj instanceof PlayReadyPlayRight)
			return new KalturaPlayReadyPlayRight();
			
		return null;
	}
}
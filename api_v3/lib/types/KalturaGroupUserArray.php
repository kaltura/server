<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGroupUserArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaGroupUserArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaGroupUser();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaGroupUser");
	}
}

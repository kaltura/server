<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.objects
 */
class KalturaVirtualEventArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct("KalturaVirtualEvent");
	}
	
	public function insert(KalturaVirtualEvent $map)
	{
		$this->array[] = $map;
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaVirtualEventArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaVirtualEvent();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
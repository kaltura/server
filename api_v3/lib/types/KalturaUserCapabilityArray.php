<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaUserCapabilityArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserCapabilityArray();
		if ($arr == null)
		{
			return $newArr;
		}
		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaUserCapability();
			$nObj->capability = $obj;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	/**
	 * @return array
	 */
	public function toObjectsArray()
	{
		$ret = array();
		foreach($this as $item)
		{
			if (isset($item->capability))
			{
				$ret[] = $item->capability;
			}
		}
		$ret = array_unique($ret);
		return $ret;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaUserCapability");
	}
}

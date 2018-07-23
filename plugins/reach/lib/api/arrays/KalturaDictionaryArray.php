<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaDictionaryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaDictionaryArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$object = new KalturaDictionary();
			$object->fromObject($obj, $responseProfile);
			$newArr[] = $object;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaDictionary");
	}
}
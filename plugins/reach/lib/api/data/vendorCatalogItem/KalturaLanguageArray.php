<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaLanguageArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaLanguageItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaLanguageArray();
		
		if($arr && is_array($arr))
		{
			foreach($arr as $item)
			{
				$arrayObject = new KalturaLanguageItem();
				$arrayObject->languages = $item;
				$newArr[] = $arrayObject;
			}
		}
		
		return $newArr;
	}
	
	public function toObjectsArray()
	{
		$ret = array();
		
		foreach ($this->toArray() as $item)
		{
			/* @var $item KalturaLanguageItem */
			$ret[] = $item->language;
		}
		
		return array_unique($ret);
	}
}
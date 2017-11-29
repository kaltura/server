<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaOutputFormatArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaOutputFormatItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaLanguageArray();
		
		if($arr && is_array($arr))
		{
			foreach($arr as $item)
			{
				$arrayObject = new KalturaOutputFormatItem();
				$arrayObject->outputFormat = $item;
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
			/* @var $item KalturaOutputFormatItem */
			$ret[] = $item->outputFormat;
		}
		
		return array_unique($ret);
	}
}
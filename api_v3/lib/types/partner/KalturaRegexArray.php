<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaRegexArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaRegexItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRegexArray();
		if (!$arr || !is_array($arr))
		{
			return $newArr;
		}
		
		foreach ($arr as $item)
		{
			if (!isset($item[0]))
			{
				continue;
			}
			$regexItem = new KalturaRegexItem();
			$regexItem->regex = $item[0];
			if (isset($item[1]))
			{
				$regexItem->description = $item[1];
			}
			$newArr[] = $regexItem;
		}
		return $newArr;
	}
}

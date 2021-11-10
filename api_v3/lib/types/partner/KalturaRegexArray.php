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
		if($arr && is_array($arr))
		{
			foreach($arr as $item)
			{
				$arrayObject = new KalturaRegexItem();
				$arrayObject->regex = $item;
				$newArr[] = $arrayObject;
			}
		}
		return $newArr;
	}
}



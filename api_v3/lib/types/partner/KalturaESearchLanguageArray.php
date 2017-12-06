<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaESearchLanguageArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaESearchLanguageItem");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchLanguageArray();
		if($arr && is_array($arr))
		{
			foreach($arr as $item)
			{
				$arrayObject = new KalturaESearchLanguageItem();
				$arrayObject->eSerachLanguage = $item;
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
			/* @var $item KalturaESearchLanguageItem */
			$ret[] = $item->eSerachLanguage;
		}

		return array_unique($ret);
	}
}



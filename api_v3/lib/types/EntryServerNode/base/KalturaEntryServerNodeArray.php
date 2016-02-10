<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryServerNodeArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaEntryServerNodeArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaEntryServerNode();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		return parent::__construct("KalturaGroupUser");
	}
}
<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRestrictionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaRestrictionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = KalturaRestrictionFactory::getInstanceByDbObject($obj);
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseRestriction");	
	}
}
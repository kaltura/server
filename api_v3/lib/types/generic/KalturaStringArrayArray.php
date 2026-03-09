<?php
/**
 * An array of KalturaStringArrayObject
 * 
 * @package api
 * @subpackage objects
 */
class KalturaStringArrayArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arrays = null)
	{
		$stringArrayArray = new KalturaStringArrayArray();
		if($arrays && is_array($arrays))
		{
			foreach($arrays as $array)
			{
				$stringArrayObject = new KalturaStringArrayObject();
				$stringArrayObject->value = KalturaStringArray::fromDbArray($array);
				$stringArrayArray[] = $stringArrayObject;
			}
		}
		return $stringArrayArray;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaStringArrayObject");
	}

}

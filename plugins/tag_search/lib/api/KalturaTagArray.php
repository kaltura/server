<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.objects
 */
class KalturaTagArray extends KalturaTypedArray
{
    /**
     * Function returns an array of API objects for the array of DB 
     * objects it is passed.
     * @param array $arr
     * @return KalturaTagArray
     */
    public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaTagArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaTag();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaTag");	
	}
}
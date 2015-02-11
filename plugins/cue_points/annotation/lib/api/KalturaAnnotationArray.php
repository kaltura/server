<?php
/**
 * @package plugins.annotation
 * @subpackage api.objects
 */
class KalturaAnnotationArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaAnnotationArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
    		$nObj = new KalturaAnnotation();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAnnotation");	
	}
}

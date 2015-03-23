<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessCaseArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaBusinessProcessCaseArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			/* @var $obj kBusinessProcessCase */
    		$nObj = new KalturaBusinessProcessCase();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBusinessProcessCase");	
	}
}
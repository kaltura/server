<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaScheduleEventRecuranceArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaScheduleEventRecuranceArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaScheduleEventRecurance();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaScheduleEventRecurance");	
	}
}
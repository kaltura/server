<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaScheduleEventRecurrenceArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaScheduleEventRecurrenceArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaScheduleEventRecurrence();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaScheduleEventRecurrence");	
	}
}
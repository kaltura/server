<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaScheduleEventArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaScheduleEventArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$newArr[] = KalturaScheduleEvent::getInstance($obj, $responseProfile);
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaScheduleEvent");	
	}
}
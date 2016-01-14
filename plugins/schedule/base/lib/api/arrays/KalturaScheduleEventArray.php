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

		// preload all parents in order to have them in the instance pool
		$parentIds = array();
		foreach ($arr as $obj)
		{
			/* @var $obj ScheduleEvent */
			if($obj->getParentId())
			{
				$parentIds[$obj->getParentId()] = true;
			} 
		}
		if(count($parentIds))
		{
			ScheduleEventPeer::retrieveByPKs(array_keys($parentIds));
		}
		
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
<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventConditionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaEventConditionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaEventCondition::getInstanceByClass(get_class($obj));
    		if(!$nObj)
    		{
    			KalturaLog::err("Event condition could not find matching type for [" . get_class($obj) . "]");
    			continue;
    		}
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEventCondition");	
	}
}
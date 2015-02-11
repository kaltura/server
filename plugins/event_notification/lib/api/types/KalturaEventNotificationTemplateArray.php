<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationTemplateArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaEventNotificationTemplateArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaEventNotificationTemplate::getInstanceByType($obj->getType());
    		if(!$nObj)
    		{
    			KalturaLog::err("Event notification template could not find matching type for [" . $obj->getType() . "]");
    			continue;
    		}
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEventNotificationTemplate");	
	}
}
<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationParameterArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaEventNotificationParameterArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaEventNotificationParameter();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEventNotificationParameter");	
	}
}
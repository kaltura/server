<?php
/**
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationParameterArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaHttpNotificationParameterArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaHttpNotificationParameter();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaHttpNotificationParameter");	
	}
}
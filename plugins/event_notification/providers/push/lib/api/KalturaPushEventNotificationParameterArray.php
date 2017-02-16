<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaPushEventNotificationParameterArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaPushEventNotificationParameterArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$parameterType = get_class($obj);
			switch ($parameterType)
			{
				case 'kPushEventNotificationParameter':
    				$nObj = new KalturaPushEventNotificationParameter();
					break;
			}
			
			if($nObj)
			{
				$nObj->fromObject($obj, $responseProfile);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPushEventNotificationParameter");	
	}
}